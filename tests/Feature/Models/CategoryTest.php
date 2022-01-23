<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class,5)->create();
     
        $categories = Category::all();
        $this->assertCount(5, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'
        ], $categoryKey);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test1'
        ]);
        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue((bool)$category->is_active);

        $category = Category::create([
            'name' => 'test2',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test3',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test3',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);

    }

    public function testUpdate()
    {
        $category = factory(Category::class,1)->create([
            'description' => 'test_description'
        ])->first();

        $data = [
            'name' => 'test1',
            'description' => 'desc1',
            'is_active' => false
        ];

        $category->update($data);
        
        foreach($data as $key => $value){
            $this->assertEquals($value, $category->{$key});
        }
    }
    
    public function testDelete()
    {
        $category = factory(Category::class)->create();
        $category->delete();
        $this->assertNull(Category::find($category->id));

        $category->restore();
        $this->assertNotNull(Category::find($category->id));
    }
}
