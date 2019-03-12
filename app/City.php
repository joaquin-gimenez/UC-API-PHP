<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    protected $table = 'city';
    
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'lastupdated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'countryname', 'lat', 'lng', 'thumburl', 'description', 'heroimage', 'budget', 'besttime', 'language', 'population', 'traveladvice', 'currency', 'tour_price'
    ];
    // protected $fillable = [
    //     'name', 'countryname'
    // ];

    // protected $guarded = ['createdate', 'lastupdated']; 

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = ['id'];
}