<?php

namespace ItmoSoft\CustomerGroups\Tests;

use ItmoSoft\CustomerGroups\CustomerGroupManager;
use ItmoSoft\CustomerGroups\Models\CustomerGroup;

class CustomerGroupManagerTest extends TestCase
{
    public function test_it_creates_a_group_and_generates_its_slug(): void
    {
        $group = app(CustomerGroupManager::class)->createGroup(['name' => 'VIP Customers']);

        $this->assertSame('VIP Customers', $group->name);
        $this->assertSame('vip-customers', $group->slug);
        $this->assertTrue($group->is_active);
    }

    public function test_it_stores_group_description_and_metadata(): void
    {
        $group = app(CustomerGroupManager::class)->createGroup([
            'name' => 'Wholesale',
            'description' => 'Wholesale account customers',
            'metadata' => ['discount_tier' => 'gold'],
        ]);

        $this->assertSame('Wholesale account customers', $group->description);
        $this->assertSame(['discount_tier' => 'gold'], $group->metadata);
    }

    public function test_it_adds_a_customer_to_a_group(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $group = app(CustomerGroupManager::class)->createGroup(['name' => 'VIP']);

        app(CustomerGroupManager::class)->addCustomer($customer, $group);

        $this->assertTrue($customer->customerGroups()->whereKey($group->id)->exists());
        $this->assertCount(1, $group->customers);
    }

    public function test_it_does_not_create_duplicate_memberships(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $group = app(CustomerGroupManager::class)->createGroup(['name' => 'VIP']);
        $manager = app(CustomerGroupManager::class);

        $manager->addCustomer($customer, $group);
        $manager->addCustomer($customer, $group);

        $this->assertSame(1, $customer->customerGroups()->count());
    }

    public function test_it_removes_a_customer_from_a_group(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $group = app(CustomerGroupManager::class)->createGroup(['name' => 'VIP']);
        $manager = app(CustomerGroupManager::class);

        $manager->addCustomer($customer, $group);
        $manager->removeCustomer($customer, $group);

        $this->assertFalse($manager->customerBelongsToGroup($customer, $group));
    }

    public function test_it_syncs_customer_groups(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $manager = app(CustomerGroupManager::class);
        $vip = $manager->createGroup(['name' => 'VIP']);
        $wholesale = $manager->createGroup(['name' => 'Wholesale']);
        $retail = $manager->createGroup(['name' => 'Retail']);

        $manager->syncCustomerGroups($customer, [$vip, $wholesale]);
        $manager->syncCustomerGroups($customer, [$retail]);

        $this->assertSame([$retail->id], $customer->customerGroups()->pluck('itmosoft_customer_groups.id')->all());
    }

    public function test_it_checks_membership_by_slug(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $group = app(CustomerGroupManager::class)->createGroup(['name' => 'VIP Customers']);
        app(CustomerGroupManager::class)->addCustomer($customer, $group);

        $this->assertTrue($customer->belongsToCustomerGroup('vip-customers'));
    }

    public function test_it_returns_only_active_groups(): void
    {
        $manager = app(CustomerGroupManager::class);
        $manager->createGroup(['name' => 'Active Group']);
        $manager->createGroup(['name' => 'Inactive Group', 'is_active' => false]);

        $this->assertCount(1, $manager->activeGroups());
    }

    public function test_it_finds_a_group_by_slug(): void
    {
        $manager = app(CustomerGroupManager::class);
        $group = $manager->createGroup(['name' => 'Wholesale']);
        $found = $manager->findGroup('wholesale');

        $this->assertSame($group->id, $found->id);
    }

    public function test_it_deletes_a_group_and_its_memberships(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $manager = app(CustomerGroupManager::class);
        $group = $manager->createGroup(['name' => 'VIP']);
        $manager->addCustomer($customer, $group);

        $manager->deleteGroup($group);

        $this->assertDatabaseMissing('itmosoft_customer_groups', ['id' => $group->id]);
        $this->assertDatabaseCount('itmosoft_customer_group_memberships', 0);
    }

    public function test_it_throws_when_group_name_is_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        app(CustomerGroupManager::class)->createGroup([]);
    }

    public function test_it_returns_groups_for_a_customer(): void
    {
        $customer = TestCustomer::query()->create(['name' => 'Adeel']);
        $manager = app(CustomerGroupManager::class);
        $manager->addCustomer($customer, $manager->createGroup(['name' => 'VIP']));
        $manager->addCustomer($customer, $manager->createGroup(['name' => 'Wholesale']));

        $this->assertCount(2, $manager->groupsForCustomer($customer));
    }
}
