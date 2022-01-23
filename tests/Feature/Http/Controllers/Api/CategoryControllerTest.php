<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Category;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;
  
    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)->assertJson($category->toArray());
    }

    public function testInvalidationData(){
        $response = $this->json('POST', route('categories.store'), []);
        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    } 

    public function testStore(){
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(201)->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));
    }

    public function testUpdate(){

        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true
        ]);
      
        $id = $response->json('id');
       
        $category = Category::find($id);

        $response->assertStatus(200)
                 ->assertJson($category->toArray())
                 ->assertJsonFragment([
                    'description' => 'test', 
                    'is_active' => true
                  ]);

    }
    
    public function testDestroy(){
        $cat = factory(Category::class)->create();
        $res = $this->json('DELETE', route('categories.destroy', ['category' => $cat->id]));
        $res->assertStatus(204);

        $this->assertNull(Category::find($cat->id));
        $this->assertNotNull(Category::withTrashed()->find($cat->id));
    }

}
