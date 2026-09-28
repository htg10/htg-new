<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['name', 'address', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function activeTenants()
    {
        return $this->hasMany(Tenant::class)->where('is_active', true);
    }

    public function payments()
    {
        return $this->hasMany(Building::class, 'property_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
