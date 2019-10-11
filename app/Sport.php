<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['title'];

    public function path()
    {
        return '/sports/' . $this->id;
    }
}
