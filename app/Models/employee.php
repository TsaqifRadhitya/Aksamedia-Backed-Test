<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    use HasUuids;

    protected $fillable = ['image', 'name', 'phone', 'divisionId', 'position'];

    protected $hidden = ['divisionId', 'created_at', 'updated_at'];

    public function division()
    {
        return $this->belongsTo(division::class, 'divisionId');
    }

}
