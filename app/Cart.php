<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $table = 'cart';
    
    const CREATED_AT = 'createdate';
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'thumburl', 'unitprice', 'totalprice'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
     protected $hidden = ['id', 'cityid', 'userid', 'createdate', 'updatedate'];

    public function city(){
        return $this->hasMany('App\City', 'cityid');
    }

}