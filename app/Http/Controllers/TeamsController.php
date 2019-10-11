<?php

namespace App\Http\Controllers;

use App\Sport;
use App\Team;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function store()
    {
        $team = Team::create($this->validateRequest());

        return redirect($team->path());
    }

    public function update(Team $team)
    {
        $team->update($this->validateRequest());

        return redirect($team->path());
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect('/teams');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'title' => 'required',
            'is_woman_team' => 'required',
            'sport_id' => 'required',
        ]);
    }
}
