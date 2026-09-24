<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Enums\RoleEnum;

class CRMRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {

        $customerRole = Role::where(
            'name',
            RoleEnum::CUSTOMER->value
        )->firstOrFail();

        $customers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'phone' => '+1-202-555-0101',
                'city' => 'New York',
                'country' => 'USA',
            ],
            [
                'name' => 'Brian Smith',
                'email' => 'brian@example.com',
                'phone' => '+1-202-555-0102',
                'city' => 'Chicago',
                'country' => 'USA',
            ],
            [
                'name' => 'Chloe Williams',
                'email' => 'chloe@example.com',
                'phone' => '+1-202-555-0103',
                'city' => 'Boston',
                'country' => 'USA',
            ],
            [
                'name' => 'Daniel Brown',
                'email' => 'daniel@example.com',
                'phone' => '+1-202-555-0104',
                'city' => 'Houston',
                'country' => 'USA',
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma@example.com',
                'phone' => '+1-202-555-0105',
                'city' => 'Seattle',
                'country' => 'USA',
            ],
            [
                'name' => 'Frank Miller',
                'email' => 'frank@example.com',
                'phone' => '+1-202-555-0106',
                'city' => 'Denver',
                'country' => 'USA',
            ],
            [
                'name' => 'Grace Wilson',
                'email' => 'grace@example.com',
                'phone' => '+1-202-555-0107',
                'city' => 'Miami',
                'country' => 'USA',
            ],
            [
                'name' => 'Henry Moore',
                'email' => 'henry@example.com',
                'phone' => '+1-202-555-0108',
                'city' => 'Austin',
                'country' => 'USA',
            ],
            [
                'name' => 'Isabella Taylor',
                'email' => 'isabella@example.com',
                'phone' => '+1-202-555-0109',
                'city' => 'Portland',
                'country' => 'USA',
            ],
            [
                'name' => 'Jack Anderson',
                'email' => 'jack@example.com',
                'phone' => '+1-202-555-0110',
                'city' => 'Atlanta',
                'country' => 'USA',
            ],
            [
                'name' => 'Karen Thomas',
                'email' => 'karen@example.com',
                'phone' => '+1-202-555-0111',
                'city' => 'Phoenix',
                'country' => 'USA',
            ],
            [
                'name' => 'Liam Jackson',
                'email' => 'liam@example.com',
                'phone' => '+1-202-555-0112',
                'city' => 'San Diego',
                'country' => 'USA',
            ],
            [
                'name' => 'Mia White',
                'email' => 'mia@example.com',
                'phone' => '+1-202-555-0113',
                'city' => 'San Francisco',
                'country' => 'USA',
            ],
            [
                'name' => 'Noah Harris',
                'email' => 'noah@example.com',
                'phone' => '+1-202-555-0114',
                'city' => 'Dallas',
                'country' => 'USA',
            ],
            [
                'name' => 'Olivia Martin',
                'email' => 'olivia@example.com',
                'phone' => '+1-202-555-0115',
                'city' => 'Washington',
                'country' => 'USA',
            ],
        ];

        foreach ($customers as $customerData) {
            $user = User::create([
                'name'     => $customerData['name'],
                'email'    => $customerData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role_id'  => $customerRole->id,
            ]);

            $customer = Customer::create(
                [
                    'user_id' => $user->id,
                    'phone'   => $customerData['phone'],
                    'address' => fake()->streetAddress(),
                    'city'    => $customerData['city'],
                    'country' => $customerData['country'],
                ]
            );

            $this->createOrdersForCustomer(
                $customer,
                $user->email,
            );
        }
    }




    private function createOrdersForCustomer(
        Customer $customer,
        string $email
    ): void {
        match ($email) {
            'alice@example.com' => $this->createEligibleOrder($customer),
            'brian@example.com' => $this->createFinalSaleOrder($customer),
            'chloe@example.com' => $this->createOldOrder($customer),
            'daniel@example.com' => $this->createHighValueOrder($customer),
            'emma@example.com' => $this->createDamagedItemOrder($customer),
            'frank@example.com' => $this->createIncorrectItemOrder($customer),
            'grace@example.com' => $this->createMixedOrder($customer),
            'henry@example.com' => $this->createSuspiciousScenarioOrder($customer),
            'isabella@example.com' => $this->createEligibleOrder($customer),
            'jack@example.com' => $this->createFinalSaleOrder($customer),
            'karen@example.com' => $this->createOldOrder($customer),
            'liam@example.com' => $this->createHighValueOrder($customer),
            'mia@example.com' => $this->createDamagedItemOrder($customer),
            'noah@example.com' => $this->createIncorrectItemOrder($customer),
            'olivia@example.com' => $this->createMixedOrder($customer),
        };
    }



    private function createEligibleOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            45000,
            now()->subDays(5)
        );

        $this->createItem(
            $order,
            'Wireless Headphones',
            1,
            45000
        );

        return $order;
    }

    private function createFinalSaleOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            25000,
            now()->subDays(7)
        );

        $this->createItem(
            $order,
            'Clearance Smart Watch',
            1,
            25000,
            true
        );

        return $order;
    }

    private function createOldOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            35000,
            now()->subDays(45)
        );

        $this->createItem(
            $order,
            'Mechanical Keyboard',
            1,
            35000
        );
        return $order;
    }



    private function createHighValueOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            75000,
            now()->subDays(4)
        );

        $this->createItem(
            $order,
            'Premium Laptop',
            1,
            75000
        );

        return $order;
    }

    private function createDamagedItemOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            30000,
            now()->subDays(3)
        );

        $this->createItem(
            $order,
            'Bluetooth Speaker',
            1,
            30000
        );

        return $order;
    }



    private function createIncorrectItemOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            40000,
            now()->subDays(6)
        );

        $this->createItem(
            $order,
            'USB-C Monitor',
            1,
            40000
        );

        return $order;
    }

    private function createMixedOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            60000,
            now()->subDays(10)
        );

        $this->createItem(
            $order,
            'Wireless Mouse',
            1,
            15000
        );

        $this->createItem(
            $order,
            'Mechanical Keyboard',
            1,
            45000
        );

        return $order;
    }




    private function createSuspiciousScenarioOrder(Customer $customer): Order
    {
        $order = $this->createOrder(
            $customer,
            20000,
            now()->subDays(2)
        );

        $this->createItem(
            $order,
            'Gaming Controller',
            1,
            20000
        );

        return $order;
    }

    private function createOrder(Customer $customer, int $totalAmountCents, Carbon $orderedAt): Order
    {
        return Order::create(
            [
                'customer_id'        => $customer->id,
                'order_number'       => 'WN-' . strtoupper(Str::random(10)),
                'total_amount_cents' => $totalAmountCents,
                'currency'           => 'USD',
                'status'             => 'completed',
                'ordered_at'         => $orderedAt,
            ]
        );
    }



    private function createItem(Order $order, string $productName, int $quantity, int $unitPriceCents, bool $isFinalSale = false): OrderItem
    {
        return OrderItem::create(
            [
                'order_id'         => $order->id,
                'product_name'     => $productName,
                'quantity'         => $quantity,
                'unit_price_cents' => $unitPriceCents,
                'is_final_sale'   => $isFinalSale,
            ]
        );
    }
}
