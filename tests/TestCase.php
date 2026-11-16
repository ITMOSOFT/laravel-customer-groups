<?php

namespace ItmoSoft\CustomerGroups\Tests;

use Illuminate\Support\Facades\Schema;
use ItmoSoft\CustomerGroups\CustomerGroupsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CustomerGroupsServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('itmosoft-customer-groups.customer_model', TestCustomer::class);
        $app['config']->set('itmosoft-customer-groups.table_prefix', 'itmosoft_');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_customers', function ($table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $this->artisan('migrate', ['--database' => 'testing'])->run();

        $this->assertTrue(Schema::hasTable('itmosoft_customer_groups'));
        $this->assertTrue(Schema::hasTable('itmosoft_customer_group_memberships'));
    }
}
