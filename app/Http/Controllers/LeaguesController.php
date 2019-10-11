<?php

namespace App\Http\Controllers;

use App\League;
use App\Sport;
use App\Team;
use Illuminate\Http\Request;

class LeaguesController extends Controller
{
    public function store()
    {
        $league = League::create($this->validateRequest());

        return redirect($league->path());
    }

    public function update(League $league)
    {
        $league->update($this->validateRequest());

        return redirect($league->path());
    }

    public function destroy(League $league)
    {
        $league->delete();

        return redirect('/leagues');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function addTeam(League $league)
    {
        $team = Team::where('id', request()
            ->validate([
                'team_id' => 'required',
                'position' => 'nullable',
            ]))->first();

        try {
            $league->addTeam($team);
        } catch (\Exception $e) { // TODO: More specific exception should be written
            return response([], 404);
        }
    }

    public function removeTeam(League $league)
    {
        $team = Team::where('id', request()->validate([
            'team_id' => 'required',
            'position' => 'nullable',
        ]))->first();

        try {
            $league->removeTeam($team);
        } catch (\Exception $e) { // TODO: More specific exception should be written
            return response([], 404);
        }
    }

    /**
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'title' => 'required|unique:leagues',
            'sport_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|greater_than_field:start_date',
            'max_team_no' => 'required|min:0',
        ]);
    }
}
