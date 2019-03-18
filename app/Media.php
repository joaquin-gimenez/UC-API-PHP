<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    protected $table = 'media';

    
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'lastupdated';


    protected $fillable = [
        'caption'
    ];






    public function city(){
        return $this->belongsTo('App\City');
    }

    public function place(){
        return $this->belongsTo('App\Place');
    }



}