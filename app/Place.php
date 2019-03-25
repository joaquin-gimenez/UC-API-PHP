<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{

    protected $table = 'place';
    
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'lastupdated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'numimages', 'timings', 'heroimage', 'herovideo'
    ];

     //protected $guarded = ['createdate', 'lastupdated']; 

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
     protected $hidden = ['createdate', 'lastupdated'];

     public function city(){
        return $this->belongsTo('App\City');
    }

    public function medias(){
        return $this->hasMany('App\Media','placeid');
    }
}