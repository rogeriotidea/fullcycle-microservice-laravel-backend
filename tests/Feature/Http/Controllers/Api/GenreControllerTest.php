<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Genre;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;
  
    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response->assertStatus(200)->assertJson($genre->toArray());
    }

    public function testInvalidationData(){
        $response = $this->json('POST', route('genres.store'), []);
        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    } 

    public function testStore(){
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response->assertStatus(201)->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
    }

    public function testUpdate(){

        $genre = factory(Genre::class)->create([
            'name' => 'test',
            'is_active' => false
        ]);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'abc',
            'is_active' => true
        ]);

        $id = $response->json('id');
          
        $genre = Genre::find($id);

        $response->assertStatus(200)
                 ->assertJson($genre->toArray())
                 ->assertJsonFragment([
                    'is_active' => true
                  ]);

    }
    
    public function testDestroy(){
        $gen = factory(Genre::class)->create();
        $res = $this->json('DELETE', route('genres.destroy', ['genre' => $gen->id]));
        $res->assertStatus(204);

        $this->assertNull(Genre::find($gen->id));
        $this->assertNotNull(Genre::withTrashed()->find($cat->id));
    }

}
