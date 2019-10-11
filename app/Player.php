<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name', 'nickname', 'position', 'country', 'date_of_birth', 'sport_id', 'team_id'];

    protected $dates = ['date_of_birth'];

    public function path()
    {
        return '/players/' . $this->id;
    }

    public function setDateOfBirthAttribute($date_of_birth)
    {
        $this->attributes['date_of_birth'] = Carbon::parse($date_of_birth);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}
