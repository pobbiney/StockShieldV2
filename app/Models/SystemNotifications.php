<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotifications extends Model
{
     protected $fillable = ['title', 'message', 'link', 'type', 'reference_id'];
}
