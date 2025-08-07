<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityOrganization extends Model
{
    protected $table = 'activity_organization';

    public $timestamps = false;

    protected $fillable = ['organization_id', 'activity_id'];
}
