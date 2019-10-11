<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeagueTeam extends Model
{
    protected $fillable = ['league_id', 'team_id', 'position'];
}
