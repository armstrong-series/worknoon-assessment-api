<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Refund Policy
    |--------------------------------------------------------------------------
    */

    'policy_version' => '1.0',

    'max_age_days' => 30,

    'human_review_threshold_cents' => 50000,

    'eligible_reasons' => [
        'damaged_item',
        'incorrect_item',
        'missing_item',
        'other',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI
    |--------------------------------------------------------------------------
    */


    'ai' => [
        'provider' => env('REFUND_AI_PROVIDER', 'groq'),

        'model' => env(
            'REFUND_AI_MODEL',
            'openai/gpt-oss-120b'
        ),

        'timeout' => (int) env(
            'REFUND_AI_TIMEOUT',
            30
        ),
    ],


];
