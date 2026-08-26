<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtraLink extends Model
{
    protected $table = 'user_extra_links';

    protected $fillable = [
        'user_id',
        'link_id',
    ];
}
