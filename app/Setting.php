<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module', 'config_key', 'config_val', 'data_type', 'label', 'description', 'resource_url'
    ];
}
