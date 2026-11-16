<?php

namespace ItmoSoft\CustomerGroups\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ItmoSoft\CustomerGroups\Models\CustomerGroup;

trait HasCustomerGroups
{
    public function customerGroups(): MorphToMany
    {
        return $this->morphToMany(
            CustomerGroup::class,
            'customer',
            (string) config('itmosoft-customer-groups.table_prefix', 'itmosoft_').'customer_group_memberships',
            'customer_id',
            'customer_group_id'
        )->withTimestamps();
    }

    public function belongsToCustomerGroup(CustomerGroup|string|int $group): bool
    {
        $query = $this->customerGroups();

        if ($group instanceof CustomerGroup) {
            return $query->whereKey($group->getKey())->exists();
        }

        return $query->where(function ($builder) use ($group): void {
            $builder->whereKey($group);

            if (is_string($group)) {
                $builder->orWhere('slug', $group);
            }
        })->exists();
    }
}
