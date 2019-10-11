<?php

namespace App\Http\Controllers;

use App\League;
use App\Sport;
use Illuminate\Http\Request;

class SportsController extends Controller
{
    public function store()
    {
        $sport = Sport::create($this->validateRequest());

        return redirect($sport->path());
    }

    public function update(Sport $sport)
    {
        $sport->update($this->validateRequest());

        return redirect($sport->path());
    }

    public function destroy(Sport $sport)
    {
        $sport->delete();

        return redirect('/sports');
    }

    public function leagues()
    {
        return $this->hasMany(League::class);
    }

    /**
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'title' => 'required|unique:sports',
        ]);
    }
}
