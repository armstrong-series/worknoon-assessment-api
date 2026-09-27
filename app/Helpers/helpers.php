<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use App\Services\RoleService;
use App\Enums\JsonApiVersion;
use Illuminate\Database\Eloquent\Builder;

if (! function_exists('worknoonResponse')) {
    function worknoonResponse(
        mixed $data,
        int $statusCode = 200,
        ?string $message = null,
        bool $status = true,
        ?string $selfLink = null,
        array $headers = [],
        ?string $type = null,
        array $included = [],
        array $meta = [],
    ): JsonResponse {

        $formatRelationship = function (mixed $related): array {
            if ($related instanceof Collection) {
                return [
                    'data' => $related
                        ->filter(fn($model) => $model instanceof Model)
                        ->map(
                            fn(Model $model): array => [
                                'type' => Str::plural(
                                    Str::snake(class_basename($model))
                                ),
                                'id' => (string) $model->getKey(),
                            ]
                        )
                        ->values()
                        ->all(),
                ];
            }

            if ($related instanceof Model) {
                return [
                    'data' => [
                        'type' => Str::plural(
                            Str::snake(class_basename($related))
                        ),
                        'id' => (string) $related->getKey(),
                    ],
                ];
            }

            return [
                'data' => null,
            ];
        };


        $formatModel = function (
            Model $resource,
            ?string $resourceType = null
        ) use ($formatRelationship): array {

            $relationships = [];

            foreach ($resource->getRelations() as $relationName => $related) {
                $relationships[$relationName] = $formatRelationship($related);
            }

            $attributes = collect(
                $resource->attributesToArray()
            )
                ->except([
                    'id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ])
                ->all();

            return [
                'type' => $resourceType
                    ?? Str::plural(
                        Str::kebab(class_basename($resource))
                    ),

                'id' => (string) $resource->getKey(),

                'attributes' => $attributes,

                'relationships' => $relationships,
            ];
        };



        if ($data instanceof Collection) {
            $formattedData = $data
                ->filter(fn($item) => $item instanceof Model)
                ->map(
                    fn(Model $resource) =>
                    $formatModel($resource, $type)
                )
                ->values()
                ->all();
        } elseif ($data instanceof Model) {
            $formattedData = $formatModel(
                $data,
                $type
            );
        } elseif (
            is_array($data)
            && array_is_list($data)
            && collect($data)->every(
                fn($item) => $item instanceof Model
            )
        ) {
            $formattedData = collect($data)
                ->map(
                    fn(Model $resource) =>
                    $formatModel($resource, $type)
                )
                ->values()
                ->all();
        } elseif (
            is_array($data)
            && ! array_is_list($data)
            && $type !== null
        ) {
            $attributes = [];
            $relationships = [];

            foreach ($data as $key => $value) {
                if ($value instanceof Model) {
                    $relationships[$key] = [
                        'data' => [
                            'type' => Str::plural(
                                Str::kebab(class_basename($value))
                            ),
                            'id' => (string) $value->getKey(),
                        ],
                    ];

                    continue;
                }

                $attributes[$key] = $value;
            }

            $formattedData = [
                'type'          => $type,
                'attributes'    => $attributes,
                'relationships' => $relationships ?: new stdClass(),
            ];
        } else {

            $formattedData = $data;
        }

        $formattedIncluded = collect($included)
            ->filter(
                fn($resource) =>
                $resource instanceof Model
            )
            ->map(
                fn(Model $resource) =>
                $formatModel($resource)
            )
            ->values()
            ->all();

        $response = [
            'message'  => $message,
            'status'   => $status ? 'success' : 'error',
            'data' => $formattedData,
            'jsonapi' => [
                'version' => JsonApiVersion::v1->value,
            ],
            'links' => [
                'self' => $selfLink ?? app('url')->current(),
            ],
        ];

        if ($formattedIncluded !== []) {
            $response['included'] = $formattedIncluded;
        }

        if ($meta !== []) {
            $response['meta'] = $meta;
        }

        return response()->json(
            $response,
            $statusCode,
            array_merge(
                [
                    'Content-Type' => 'application/vnd.api+json',
                ],
                $headers
            )
        );
    }
}



if (! function_exists('authorizedRole')) {

    function authorizedRole(string|array $roles, $user = null): void
    {
        $user = $user ?? auth()->user();
        $roleService = app()->make(RoleService::class);

        foreach ((array) $roles as $role) {
            if ($roleService->userHasRole($user, $role)) {
                return;
            }
        }

        throw new AuthorizationException('Only ' . implode(', ', (array) $roles) . ' authorized action!');
    }
}


if (! function_exists('queryFilter')) {

    function queryFilter(Builder $query, array $filters, array $allowedFilters): Builder
    {
        foreach ($filters as $field => $value) {
            if (
                ! array_key_exists($field, $allowedFilters) ||
                $value === null ||
                $value === ''
            ) {
                continue;
            }

            $column = $allowedFilters[$field];

            if (is_callable($column)) {
                $column($query, $value);

                continue;
            }

            $query->where($column, $value);
        }

        return $query;
    }
}
