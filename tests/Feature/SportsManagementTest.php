<?php

namespace Tests\Feature;

use App\Sport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function Symfony\Component\Debug\Tests\testHeader;

class SportsManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_sport_can_be_created()
    {
        $response = $this->post('/sports', $this->data());

        $this->assertCount(1, Sport::all());

        $sport = Sport::first();

        $response->assertRedirect($sport->path());
    }

    /** @test */
    public function a_sport_title_is_required_to_create()
    {
        $response = $this->post('/sports', array_merge($this->data(), ['title' => '']));

        $response->assertSessionHasErrors('title');
        $this->assertCount(0, Sport::all());
    }

    /** @test */
    public function a_sport_title_must_be_unique()
    {
        $this->post('/sports', $this->data());

        $response = $this->post('/sports', $this->data());

        $response->assertSessionHasErrors('title');
        $this->assertCount(1, Sport::all());
    }

    /** @test */
    public function a_sport_can_be_updated()
    {
        $this->post('/sports', $this->data());
        $newTitle = 'New Sports Title';

        $sport = Sport::first();
        $response = $this->patch($sport->path(), [
            'title' => $newTitle,
        ]);

        $this->assertEquals($newTitle, $sport->fresh()->title);

        $response->assertRedirect($sport->fresh()->path());
    }

    /** @test */
    public function a_sport_can_be_deleted()
    {
        $this->post('/sports', $this->data());
        $sport = Sport::first();

        $response = $this->delete($sport->path());

        $this->assertCount(0, Sport::all());
        $response->assertRedirect('/sports');
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return [
            'title' => 'Cool Sport',
        ];
    }
}
