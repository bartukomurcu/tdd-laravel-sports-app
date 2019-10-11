<?php

namespace App\Http\Controllers;

use App\Event;
use App\Team;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function store()
    {
        $this->validateRequest();
        $team1_id = request()->input('home_team_id');
        $team2_id = request()->input('away_team_id');
        $sport_id = request()->input('sport_id');
        $team1 = Team::where('id', $team1_id)->first();
        $team2 = Team::where('id', $team2_id)->first();

        if (is_null($team1) || is_null($team2) || $sport_id !== $team1->sport_id || $sport_id !== $team2->sport_id) {
            return redirect(404);
        }
        $event = Event::create($this->validateRequest());

        return redirect($event->path());
    }

    public function update(Event $event)
    {
        $event->update($this->validateRequest());

        return redirect($event->path());
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect('/events');
    }

    /**
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'date_time' => 'required',
            'home_team_id' => 'required',
            'away_team_id' => 'required|different:home_team_id',
            'sport_id' => 'required',
            'home_team_score' => 'nullable',
            'away_team_score' => 'nullable',
        ]);
    }
}
