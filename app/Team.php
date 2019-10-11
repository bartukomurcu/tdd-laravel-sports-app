<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['title', 'is_woman_team', 'sport_id'];

    public function path()
    {
        return '/teams/' . $this->id;
    }
}
