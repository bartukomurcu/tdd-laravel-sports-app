<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['date_time', 'home_team_id', 'away_team_id', 'sport_id', 'home_team_score', 'away_team_score', ];

    protected $dates = ['date_time'];

    public function path()
    {
        return '/events/' . $this->id;
    }

    public function setDateTimeAttribute($date_time)
    {
        $this->attributes['date_time'] = Carbon::parse($date_time);
    }

    public function homeTeamScore(?int $score)
    {
        //TODO: Change scores by looking at the sport
        if ($score) {
            $this->home_team_score += $score;
        } else {
            $this->home_team_score += 1;
        }
    }
}
