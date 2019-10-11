<?php

namespace Tests\Feature;

use App\Player;
use App\Sport;
use App\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_player_can_be_added()
    {
        $sport = factory(Sport::class)->create();
        $team = factory(Team::class)->create();
        $response = $this->post('/players', $this->data($sport->id, $team->id));

        $this->assertCount(1, Player::all());

        $player = Player::first();

        $response->assertRedirect($player->path());
    }

    /** @test */
    public function a_player_needs_a_real_sport_id_to_be_added()
    {
        $team = factory(Team::class)->create();
        try {
            $this->post('/players', $this->data(5, $team->id));
        } catch (\PDOException $pdo) {
            if ($pdo->errorInfo)
                $this->assertEmpty(null);
        }

        $this->assertCount(0, Player::all());
    }

    /** @test */
    public function team_id_has_to_be_real_if_added()
    {
        $sport = factory(Sport::class)->create();
        try {
            $this->post('/players', $this->data($sport->id, 5));
        } catch (\PDOException $pdo) {
            if ($pdo->errorInfo)
                $this->assertEmpty(null);
        }

        $this->assertCount(0, Player::all());
    }

    /** @test */
    public function players_sport_id_and_teams_sport_id_has_to_be_same_if_added()
    {
        $sport = factory(Sport::class)->create();
        $sport2 = factory(Sport::class)->create();
        $this->post('/teams', [
            'title' => 'Cool Team',
            'is_woman_team' => 0,
            'sport_id' => $sport2->id,
        ]);
        $team = Team::first();

        try {
            $this->post('/players', $this->data($sport->id, $team->id));
        } catch (\PDOException $pdo) {
            if ($pdo->errorInfo)
                $this->assertEmpty(null);
        }

        $this->assertCount(0, Player::all());
    }

    /** @test */
    public function a_player_needs_a_name_to_be_added()
    {
        $sport = factory(Sport::class)->create();
        $team = factory(Team::class)->create();

        $response = $this->post('/players', array_merge($this->data($sport->id, $team->id), ["name" => ""]));

        $this->assertCount(0, Player::all());
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_player_can_be_added_with_only_required_values()
    {
        $sport = factory(Sport::class)->create();
        $response = $this->post('/players',
            array_merge($this->data($sport->id), [
                'nickname' => '',
                'position' => '',
                'country' => '',
                'date_of_birth' => '']));

        $this->assertCount(1, Player::all());

        $player = Player::first();

        $response->assertRedirect($player->path());
    }

    /** @test */
    public function a_player_can_be_updated()
    {
        $sport = factory(Sport::class)->create();
        $team = factory(Team::class)->create();
        $this->post('/players', $this->data($sport->id, $team->id));

        $sport2 = factory(Sport::class)->create();
        $team2 = factory(Team::class)->create();
        $player = Player::first();
        $response = $this->patch($player->path(), [
            'name' => 'Best Player',
            'nickname' => 'Komurcu',
            'position' => 'GK',
            'country' => 'UK',
            'date_of_birth' => '12/18/1998',
            'sport_id' => $sport2->id,
            'team_id' => $team2->id,

        ]);

        $this->assertEquals('Best Player', $player->fresh()->name);
        $this->assertEquals('Komurcu', $player->fresh()->nickname);
        $this->assertEquals('GK', $player->fresh()->position);
        $this->assertEquals('UK', $player->fresh()->country);
        $this->assertInstanceOf(Carbon::class, $player->fresh()->date_of_birth);
        $this->assertEquals('1998/18/12', $player->fresh()->date_of_birth->format('Y/d/m'));
        $this->assertEquals($sport2->id, $player->fresh()->sport_id);
        $this->assertEquals($team2->id, $player->fresh()->team_id);

        $response->assertRedirect($player->fresh()->path());
    }

    /** @test */
    public function a_player_can_be_deleted()
    {
        $sport = factory(Sport::class)->create();
        $team = factory(Team::class)->create();
        $this->post('/players', $this->data($sport->id, $team->id));
        $player = Player::first();

        $response = $this->delete($player->path());

        $this->assertCount(0, Player::all());
        $response->assertRedirect('/players');
    }

    /**
     * Required data array to create a player
     * @param int $sport_id
     * @param int $team_id
     * @return array
     */
    public function data(int $sport_id, int $team_id = null): array
    {
        return [
            'name' => 'Bartu Komurcu',
            'nickname' => 'Bartu',
            'position' => 'ST',
            'country' => 'Turkey',
            'date_of_birth' => '09.12.1994',
            'sport_id' => $sport_id,
            'team_id' => $team_id,
        ];
    }
}
