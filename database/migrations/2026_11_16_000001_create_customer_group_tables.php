<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = (string) config('itmosoft-customer-groups.table_prefix', 'itmosoft_');
        $groupsTable = $prefix.'customer_groups';
        $membershipsTable = $prefix.'customer_group_memberships';

        if (! Schema::hasTable($groupsTable)) {
            Schema::create($groupsTable, function (Blueprint $table): void {
                $table->id();
                $table->string('name', 120);
                $table->string('slug', 140)->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable($membershipsTable)) {
            Schema::create($membershipsTable, function (Blueprint $table) use ($groupsTable): void {
                $table->id();
                $table->unsignedBigInteger('customer_group_id');
                $table->morphs('customer', 'customer_group_customer_morph_index');
                $table->timestamps();

                $table->foreign('customer_group_id')
                    ->references('id')
                    ->on($groupsTable)
                    ->cascadeOnDelete();

                $table->unique(
                    ['customer_group_id', 'customer_type', 'customer_id'],
                    'customer_group_membership_unique'
                );
                $table->index(['customer_type', 'customer_id'], 'customer_group_customer_lookup');
            });
        }
    }

    public function down(): void
    {
        $prefix = (string) config('itmosoft-customer-groups.table_prefix', 'itmosoft_');
        Schema::dropIfExists($prefix.'customer_group_memberships');
        Schema::dropIfExists($prefix.'customer_groups');
    }
};
