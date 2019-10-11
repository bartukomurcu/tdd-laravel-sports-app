<?php

namespace Tests\Feature;

use App\League;
use App\LeagueTeam;
use App\Sport;
use App\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeagueManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_league_can_be_created()
    {
        $sport = factory(Sport::class)->create();
        $response = $this->post('/leagues', $this->data($sport->id));

        $leagues = League::all();
        $league = $leagues->first();

        $this->assertCount(1, $leagues);
        $this->assertInstanceOf(Carbon::class, $league->start_date);
        $this->assertEquals('2018/14/03', $league->start_date->format('Y/d/m'));
        $this->assertInstanceOf(Carbon::class, $league->end_date);
        $this->assertEquals('2018/14/03', $league->start_date->format('Y/d/m'));

        $response->assertRedirect($league->path());
    }

    /** @test */
    public function a_league_needs_sport_id_to_be_created()
    {
        try {
            $this->post('/leagues', $this->data(0));
        } catch (\PDOException $pdo) {
            if ($pdo->errorInfo)
            $this->assertEmpty(null);
        }

        $this->assertCount(0, League::all());
    }

    /** @test */
    public function a_league_needs_title_to_be_created()
    {
        $sport = factory(Sport::class)->create();

        $response = $this->post('/leagues', array_merge($this->data($sport->id), ["title" => ""]));

        $this->assertCount(0, League::all());
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_league_needs_unique_title_to_be_created()
    {
        $sport = factory(Sport::class)->create();
        $this->post('/leagues', $this->data($sport->id));

        $response = $this->post('/leagues', $this->data($sport->id));

        $this->assertCount(1, League::all());
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_league_needs_start_date_to_be_created()
    {
        $sport = factory(Sport::class)->create();

        $response = $this->post('/leagues', array_merge($this->data($sport->id), ["start_date" => ""]));

        $this->assertCount(0, League::all());
        $response->assertSessionHasErrors('start_date');
    }

    /** @test */
    public function a_league_needs_end_date_to_be_created()
    {
        $sport = factory(Sport::class)->create();

        $response = $this->post('/leagues', array_merge($this->data($sport->id), ["end_date" => ""]));

        $this->assertCount(0, League::all());
        $response->assertSessionHasErrors('end_date');
    }

    /** @test */
    public function a_league_needs_max_team_no_to_be_created()
    {
        $sport = factory(Sport::class)->create();

        $response = $this->post('/leagues', array_merge($this->data($sport->id), ["max_team_no" => ""]));

        $this->assertCount(0, League::all());
        $response->assertSessionHasErrors('max_team_no');
    }

    /** @test */
    public function max_team_no_must_be_greater_than_or_equal_to_0()
    {
        $sport = factory(Sport::class)->create();

        $this->post('/leagues', array_merge($this->data($sport->id), ["max_team_no" => "-1"]));

        $this->assertCount(0, League::all());
    }

    /** @test */
    public function end_date_must_be_bigger_than_or_equal_to_start_date()
    {
        $sport = factory(Sport::class)->create();

        $this->post('/leagues', array_merge($this->data($sport->id), ["start_date" => "09.12.1994", "end_date" => "08.10.1994"]));

        $this->assertCount(0, League::all());
    }

    /** @test */
    public function a_league_can_be_updated()
    {
        $sport = factory(Sport::class)->create();
        $this->post('/leagues', $this->data($sport->id));

        $sport2 = factory(Sport::class)->create();
        $league = League::first();
        $response = $this->patch($league->path(), [
            'title' => 'Best League',
            'sport_id' => $sport2->id,
            'start_date' => '03/15/2019',
            'end_date' => '03/15/2019',
            'max_team_no' => '14',
        ]);

        $this->assertEquals('Best League', $league->fresh()->title);
        $this->assertEquals($sport2->id, $league->fresh()->sport_id);
        $this->assertInstanceOf(Carbon::class, $league->fresh()->start_date);
        $this->assertEquals('2019/15/03', $league->fresh()->start_date->format('Y/d/m'));
        $this->assertInstanceOf(Carbon::class, $league->fresh()->end_date);
        $this->assertEquals('2019/15/03', $league->fresh()->end_date->format('Y/d/m'));
        $this->assertEquals(14, $league->fresh()->max_team_no);

        $response->assertRedirect($league->fresh()->path());
    }

    /** @test */
    public function a_league_can_be_deleted()
    {
        $sport = factory(Sport::class)->create();
        $this->post('/leagues', $this->data($sport->id));
        $league = League::first();

        $response = $this->delete($league->path());

        $this->assertCount(0, League::all());
        $response->assertRedirect('/leagues');
    }

    /** @test */
    public function a_team_can_be_added_to_a_league_feature()
    {
        $league = factory(League::class)->create();
        $team = factory(Team::class)->create();

        $this->post($league->teamsPath(), [
            'team_id' => $team->id,
        ]);

        $this->assertCount(1, LeagueTeam::all());
        $this->assertEquals($league->id, LeagueTeam::first()->league_id);
        $this->assertEquals($team->id, LeagueTeam::first()->team_id);
    }

    /** @test */
    public function a_team_can_be_removed_from_a_league_feature()
    {
        $league = factory(League::class)->create();
        $team = factory(Team::class)->create();
        $league->addTeam($team);

        $this->delete($league->teamsPath(), [
            'team_id' => $team->id,
        ]);

        $this->assertCount(0, LeagueTeam::all());
    }

    /** @test */
    public function a_404_is_thrown_if_a_team_is_tried_to_be_added_twice()
    {
        $league = factory(League::class)->create();
        $team = factory(Team::class)->create();
        $this->post($league->teamsPath(), [
            'team_id' => $team->id,
        ]);

        $this->post($league->teamsPath(), [
                                    'team_id' => $team->id,
                                ])
                        ->assertStatus(404);

        $this->assertCount(1, LeagueTeam::all());
        $this->assertEquals($league->id, LeagueTeam::first()->league_id);
        $this->assertEquals($team->id, LeagueTeam::first()->team_id);
    }

    /** @test */
    public function the_team_must_be_doing_the_same_sport_as_the_league()
    {
        $league = factory(League::class)->create();
        $sport = factory(Sport::class)->create();
        $this->post('/teams', [
                                        'title' => 'Cool Team',
                                        'is_woman_team' => 0,
                                        'sport_id' => $sport->id,
                                    ]);
        $team = Team::first();

        $this->post($league->teamsPath(), [
            'team_id' => $team->id,
        ])
            ->assertStatus(404);

        $this->assertCount(0, LeagueTeam::all());
    }

    /**
     * @param int $sport_id
     * @return array
     */
    public function data(int $sport_id): array
    {
        return [
            'title' => 'Cool League',
            'sport_id' => $sport_id,
            'start_date' => '03/14/2018',
            'end_date' => '03/14/2018',
            'max_team_no' => '12',
        ];
    }
}
