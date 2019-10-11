<?php

namespace Tests\Feature;

use App\Sport;
use App\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_team_can_be_created()
    {
        $sport = factory(Sport::class)->create();
        $response = $this->post('/teams', $this->data($sport->id));

        $this->assertCount(1, Team::all());

        $team = Team::first();

        $response->assertRedirect($team->path());
    }

    /** @test */
    public function a_team_title_is_required_to_create()
    {
        $sport = factory(Sport::class)->create();
        $response = $this->post('/teams', array_merge($this->data($sport->id), ['title' => '']));

        $response->assertSessionHasErrors('title');
        $this->assertCount(0, Team::all());
    }

    /** @test */
    public function is_woman_team_is_required_to_create()
    {
        $sport = factory(Sport::class)->create();
        $response = $this->post('/teams', array_merge($this->data($sport->id), ['is_woman_team' => '']));

        $response->assertSessionHasErrors('is_woman_team');
        $this->assertCount(0, Team::all());
    }

    /** @test */
    public function sport_id_is_required_to_create()
    {
        $sport = factory(Sport::class)->create();
        $response = $this->post('/teams', array_merge($this->data($sport->id), ['sport_id' => '']));

        $response->assertSessionHasErrors('sport_id');
        $this->assertCount(0, Team::all());
    }

    /** @test */
    public function team_title_and_sport_id_must_be_unique()
    {
        $sport = factory(Sport::class)->create();
        $this->post('/teams', $this->data($sport->id));

        $this->post('/teams', $this->data($sport->id));

        $this->assertCount(1, Team::all());
    }

    /** @test */
    public function same_team_titles_in_different_sports_are_ok()
    {
        $sport = factory(Sport::class)->create();
        $sport2 = factory(Sport::class)->create();
        $this->post('/teams', $this->data($sport->id));
        $this->post('/teams', $this->data($sport2->id));

        $this->assertCount(2, Team::all());
    }

    /** @test */
    public function a_team_can_be_updated()
    {
        $sport = factory(Sport::class)->create();
        $sport2 = factory(Sport::class)->create();
        $this->post('/teams', $this->data($sport->id));

        $team = Team::first();
        $response = $this->patch($team->path(), [
            'title' => 'Best League',
            'is_woman_team' => 1,
            'sport_id' => $sport2->id,
        ]);

        $this->assertEquals('Best League', $team->fresh()->title);
        $this->assertEquals(1, $team->fresh()->is_woman_team);
        $this->assertEquals($sport2->id, $team->fresh()->sport_id);

        $response->assertRedirect($team->fresh()->path());
    }

    /** @test */
    public function a_team_can_be_deleted()
    {
        $sport = factory(Sport::class)->create();
        $this->post('/teams', $this->data($sport->id));
        $team = Team::first();

        $response = $this->delete($team->path());

        $this->assertCount(0, Team::all());
        $response->assertRedirect('/teams');
    }

    /**
     * @param int $sport_id
     * @return array
     */
    public function data(int $sport_id): array
    {
        return [
            'title' => 'Cool Team',
            'is_woman_team' => 0,
            'sport_id' => $sport_id,
        ];
    }
}
