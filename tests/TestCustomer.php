<?php

namespace ItmoSoft\CustomerGroups\Tests;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\CustomerGroups\Concerns\HasCustomerGroups;

class TestCustomer extends Model
{
    use HasCustomerGroups;

    protected $table = 'test_customers';

    protected $guarded = [];
}
