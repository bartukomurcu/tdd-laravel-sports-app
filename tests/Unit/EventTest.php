<?php

namespace Tests\Unit;

use App\Event;
use App\Sport;
use App\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function score_conceded()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $event = factory(Event::class)->create([
            'sport_id' => $sport->id,
            'home_team_id' => $team1->id,
            'away_team_id' => $team2->id,
        ]);

        $event->homeTeamScore(1);

        $this->assertCount(1, Event::all());
        $this->assertEquals(1, $event->home_team_score);
    }
}
