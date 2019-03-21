<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'order';

    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'updatedate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orderid', 'cityid', 'userid', 'thumburl', 'unitprice', 'quantity', 'totalprice'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['createdate', 'updatedate'];
}
