<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status_id',
        'due_date'
    ];
    protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tasks');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
