<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'color_code', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
