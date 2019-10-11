<?php

namespace App\Http\Controllers;

use App\Player;
use App\Team;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public function store()
    {
        $this->validateRequest();
        $team_id = request()->input('team_id');
        $sport_id = request()->input('sport_id');
        if ($team_id) {
            $team = Team::where('id', $team_id)->first();
            if (is_null($team) || $sport_id !== $team->sport_id) {
                return redirect(404);
            }
        }
        $player = Player::create($this->validateRequest());

        return redirect($player->path());
    }

    public function update(Player $player)
    {
        $player->update($this->validateRequest());

        return redirect($player->path());
    }

    public function destroy(Player $player)
    {
        $player->delete();

        return redirect('/players');
    }

    /**
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'name' => 'required',
            'nickname' => 'nullable',
            'position' => 'nullable',
            'country' => 'nullable',
            'date_of_birth' => 'nullable',
            'sport_id' => 'required',
            'team_id' => 'nullable',
        ]);
    }
}
