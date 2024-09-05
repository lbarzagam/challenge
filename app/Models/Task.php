<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    
    protected $fillable = ['title','description','completed','project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function casts(): array
    {
        return [
            'completed'=>'boolean'
        ];
    }
    
}
