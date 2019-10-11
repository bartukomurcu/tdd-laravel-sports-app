<?php

namespace Tests\Feature;

use App\Event;
use App\Sport;
use App\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_can_be_created()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();

        $response = $this->post('/events', $this->data($sport->id, $team1->id, $team2->id));

        $events = Event::all();
        $event = $events->first();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(Carbon::class, $event->date_time);
        $this->assertEquals('1994/12/09', $event->date_time->format('Y/d/m'));
        $this->assertEquals($team1->id, $event->home_team_id);
        $this->assertEquals($team2->id, $event->away_team_id);

        $response->assertRedirect($event->path());
    }

    /** @test */
    public function a_date_time_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $response = $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['date_time' => '']));

        $response->assertSessionHasErrors('date_time');
        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function a_home_team_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $response = $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['home_team_id' => '']));

        $response->assertSessionHasErrors('home_team_id');
        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function an_away_team_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $response = $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['away_team_id' => '']));

        $response->assertSessionHasErrors('away_team_id');
        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function a_sport_id_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $response = $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['sport_id' => '']));

        $response->assertSessionHasErrors('sport_id');
        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function a_real_home_team_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['home_team_id' => '5']));

        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function a_real_away_team_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['away_team_id' => '5']));

        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function a_real_sport_id_is_required_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $this->post('/events', array_merge($this->data($sport->id, $team1->id, $team2->id), ['sport_id' => '5']));

        $this->assertCount(0, Event::all());
    }

    /** @test */
    public function sport_ids_must_be_same_to_create_an_event()
    {
        $sport = factory(Sport::class)->create();
        $sport2 = factory(Sport::class)->create();
        $this->post('/teams', [
            'title' => 'Cool Team',
            'is_woman_team' => 0,
            'sport_id' => $sport->id,
        ]);
        $team1 = Team::first();
        $this->post('/teams', [
            'title' => 'Cool2 Team',
            'is_woman_team' => 0,
            'sport_id' => $sport2->id,
        ]);
        $team2 = Team::orderBy('id','desc')->first();

        $response = $this->post('/events', $this->data($sport->id, $team1->id, $team2->id));

        $this->assertCount(0, Event::all());
        $response->assertRedirect(404);
    }

    /** @test */
    public function an_event_can_be_updated()
    {
        $sport1 = factory(Sport::class)->create();
        $sport2 = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $team3 = factory(Team::class)->create();
        $team4 = factory(Team::class)->create();
        $this->post('/events', $this->data($sport1->id, $team1->id, $team2->id));
        $event = Event::first();

        $response = $this->patch($event->path(), [
            'date_time' => '10.09.1994',
            'home_team_id' => $team3->id,
            'away_team_id' => $team4->id,
            'sport_id' => $sport2->id,
            'home_team_score' => '2',
            'away_team_score' => '1',
        ]);

        $this->assertCount(1, Event::all());
        $this->assertInstanceOf(Carbon::class, $event->fresh()->date_time);
        $this->assertEquals('1994/10/09', $event->fresh()->date_time->format('Y/d/m'));
        $this->assertEquals($team3->id, $event->fresh()->home_team_id);
        $this->assertEquals($team4->id, $event->fresh()->away_team_id);
        $this->assertEquals('2', $event->fresh()->home_team_score);
        $this->assertEquals('1', $event->fresh()->away_team_score);

        $response->assertRedirect($event->path());
    }

    /** @test */
    public function an_event_can_be_deleted()
    {
        $sport = factory(Sport::class)->create();
        $team1 = factory(Team::class)->create();
        $team2 = factory(Team::class)->create();
        $this->post('/events', $this->data($sport->id, $team1->id, $team2->id));
        $event = Event::first();

        $response = $this->delete($event->path());

        $this->assertCount(0, Event::all());
        $response->assertRedirect('/events');
    }


    /**
     * Required data array to create a player
     * @param int $sport_id
     * @param int $team1_id
     * @param int $team2_id
     * @return array
     */
    public function data(int $sport_id, int $team1_id, int $team2_id): array
    {
        return [
            'date_time' => '12.09.1994',
            'home_team_id' => $team1_id,
            'away_team_id' => $team2_id,
            'sport_id' => $sport_id,
            'home_team_score' => '0',
            'away_team_score' => '0',
        ];
    }
}
