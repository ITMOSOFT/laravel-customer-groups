<?php

namespace ItmoSoft\CustomerGroups;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ItmoSoft\CustomerGroups\Models\CustomerGroup;

class CustomerGroupManager
{
    public function createGroup(array $attributes): CustomerGroup
    {
        if (blank($attributes['name'] ?? null)) {
            throw new InvalidArgumentException('A customer group name is required.');
        }

        $attributes['slug'] = $attributes['slug'] ?? Str::slug($attributes['name']);

        return CustomerGroup::query()->create(Arr::only($attributes, [
            'name', 'slug', 'description', 'is_active', 'metadata',
        ]));
    }

    public function findGroup(CustomerGroup|string|int $group): CustomerGroup
    {
        if ($group instanceof CustomerGroup) {
            return $group;
        }

        $query = CustomerGroup::query();

        $found = is_int($group) || ctype_digit((string) $group)
            ? $query->find($group)
            : $query->where('slug', $group)->first();

        if (! $found) {
            throw new InvalidArgumentException('The requested customer group does not exist.');
        }

        return $found;
    }

    public function addCustomer(Model $customer, CustomerGroup|string|int $group): void
    {
        $this->findGroup($group)->customers()->syncWithoutDetaching([$customer->getKey()]);
    }

    public function removeCustomer(Model $customer, CustomerGroup|string|int $group): void
    {
        $this->findGroup($group)->customers()->detach($customer->getKey());
    }

    /**
     * @param array<int, CustomerGroup|int|string> $groups
     */
    public function syncCustomerGroups(Model $customer, array $groups): void
    {
        $ids = collect($groups)
            ->map(fn (CustomerGroup|int|string $group): int|string => $this->findGroup($group)->getKey())
            ->unique()
            ->values()
            ->all();

        DB::transaction(fn () => $customer->customerGroups()->sync($ids));
    }

    public function customerBelongsToGroup(Model $customer, CustomerGroup|string|int $group): bool
    {
        return $customer->customerGroups()
            ->whereKey($this->findGroup($group)->getKey())
            ->exists();
    }

    public function groupsForCustomer(Model $customer)
    {
        return $customer->customerGroups()->orderBy('name')->get();
    }

    public function deleteGroup(CustomerGroup|string|int $group): void
    {
        $this->findGroup($group)->delete();
    }

    public function activeGroups()
    {
        return CustomerGroup::query()->where('is_active', true)->orderBy('name')->get();
    }
}
