<?php

namespace ItmoSoft\CustomerGroups\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CustomerGroup extends Model
{
    protected $guarded = ['id'];

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function getTable(): string
    {
        return (string) Config::get('itmosoft-customer-groups.table_prefix', 'itmosoft_').'customer_groups';
    }

    public function customers(): MorphToMany
    {
        $customerModel = (string) Config::get('itmosoft-customer-groups.customer_model');

        return $this->morphedByMany(
            $customerModel,
            'customer',
            (string) Config::get('itmosoft-customer-groups.table_prefix', 'itmosoft_').'customer_group_memberships',
            'customer_group_id',
            'customer_id'
        )->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function (CustomerGroup $group): void {
            if (blank($group->slug) && filled($group->name)) {
                $group->slug = Str::slug($group->name);
            }
        });
    }
}
