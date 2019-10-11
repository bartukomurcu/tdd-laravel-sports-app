<?php

namespace Tests\Unit;

use App\League;
use App\LeagueTeam;
use App\Sport;
use App\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueTeamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_team_can_be_added_to_a_league()
    {
        $league = factory(League::class)->create();
        $team = factory(Team::class)->create();

        $league->addTeam($team);

        $this->assertCount(1, LeagueTeam::all());
        $this->assertEquals($league->id, LeagueTeam::first()->league_id);
        $this->assertEquals($team->id, LeagueTeam::first()->team_id);
    }

    /** @test */
    public function a_team_can_be_removed_from_a_league()
    {
        $league = factory(League::class)->create();
        $team = factory(Team::class)->create();
        $league->addTeam($team);

        $league->removeTeam($team);

        $this->assertCount(0, LeagueTeam::all());
    }

    /** @test */
    public function a_team_can_be_added_to_a_league_only_once()
    {
        $this->expectException(\Exception::class);

        $league = factory(League::class)->create();
        $team = factory(Team::class)->create();
        $league->addTeam($team);

        $league->addTeam($team);

        $this->assertCount(1, LeagueTeam::all());
    }
}
