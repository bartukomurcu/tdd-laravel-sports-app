<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = ['title', 'sport_id', 'start_date', 'end_date', 'max_team_no'];

    protected $dates = ['start_date', 'end_date'];

    public function path()
    {
        return '/leagues/' . $this->id;
    }

    public function setStartDateAttribute($start_date)
    {
        $this->attributes['start_date'] = Carbon::parse($start_date);
    }

    public function setEndDateAttribute($end_date)
    {
        $this->attributes['end_date'] = Carbon::parse($end_date);
    }

    public function leagues()
    {
        return $this->hasMany(LeagueTeam::class);
    }

    public function teamsPath()
    {
        return '/leagues/' . $this->id . '/teams';
    }

    public function addTeam(Team $team): LeagueTeam
    {
        if (is_null($this->isTeamExist($team->id)) || $this->isTeamAlreadyJoined($team->id) || $this->sport_id !== $team->sport_id) {
            throw new \Exception();
        }

        $leagueTeam = $this->leagues()->create([
            'league_id' => $this->id,
            'team_id' => $team->id,
        ]);

        return $leagueTeam;
    }

    public function removeTeam(Team $team)
    {
        if (is_null($this->isTeamExist($team->id))  || is_null($this->isTeamAlreadyJoined($team->id))) {
            throw new \Exception();
        }

        $this->leagues()->where('team_id', $team->id)->where('league_id', $this->id)->first()->delete();
    }

    public function isTeamExist(int $team_id): ?Team
    {
        return Team::where('id', $team_id)->first();
    }

    public function isTeamAlreadyJoined(int $team_id)
    {
        return $this->leagues()->where('team_id', $team_id)->first();
    }
}
