# Laravel Customer Groups

Customer segmentation and group membership management for Laravel applications.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-customer-groups
php artisan vendor:publish --tag=itmosoft-customer-groups-config
php artisan migrate
```

## Configuration

The published `config/itmosoft-customer-groups.php` file contains:

```php
return [
    'table_prefix' => 'itmosoft_',
    'customer_model' => App\Models\User::class,
];
```

Set `customer_model` to the Eloquent model representing customers in your application.

## Add the customer trait

Add `HasCustomerGroups` to your customer model:

```php
use ItmoSoft\CustomerGroups\Concerns\HasCustomerGroups;

class Customer extends Model
{
    use HasCustomerGroups;
}
```

The relationship is polymorphic, so customer models can use their own table and primary key.

## Create and manage groups

```php
use ItmoSoft\CustomerGroups\CustomerGroupManager;

$manager = app(CustomerGroupManager::class);

$vip = $manager->createGroup([
    'name' => 'VIP Customers',
    'description' => 'Customers eligible for VIP benefits',
    'metadata' => ['tier' => 'gold'],
]);

$manager->addCustomer($customer, $vip);
$manager->removeCustomer($customer, $vip);
$manager->syncCustomerGroups($customer, [$vip]);

$groups = $manager->groupsForCustomer($customer);
$isVip = $customer->belongsToCustomerGroup('vip-customers');
```

Groups can also be resolved by their numeric ID or slug:

```php
$group = $manager->findGroup('vip-customers');
$activeGroups = $manager->activeGroups();
```

## Eloquent relationships

Customer model:

```php
$customer->customerGroups;
$customer->customerGroups()->where('is_active', true)->get();
```

Customer group model:

```php
use ItmoSoft\CustomerGroups\Models\CustomerGroup;

$group = CustomerGroup::where('slug', 'vip-customers')->firstOrFail();
$group->customers;
```

## Database tables

By default, the package creates:

- `itmosoft_customer_groups`
- `itmosoft_customer_group_memberships`

The membership table is polymorphic and supports multiple customer model classes. A composite unique index prevents duplicate memberships.

Changing `table_prefix` after migrations have run requires a corresponding database migration; do not change it casually in an existing installation.

## Integration boundaries

This package owns customer groups and memberships only. It does not implement customer records, price calculation, discount application, or promotion redemption. Applications can use group membership as an input to `laravel-pricing` or `laravel-promotions`.

## Testing

```bash
composer install
composer test
```

## License

MIT.
