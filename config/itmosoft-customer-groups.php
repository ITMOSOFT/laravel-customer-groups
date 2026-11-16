<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database table prefix
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'itmosoft_',

    /*
    |--------------------------------------------------------------------------
    | Customer model
    |--------------------------------------------------------------------------
    | Customer models should use the HasCustomerGroups trait. The package uses
    | a polymorphic pivot, so any Eloquent model can be a customer.
    */
    'customer_model' => App\Models\User::class,
];
