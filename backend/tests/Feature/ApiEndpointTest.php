<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiEndpointTest extends TestCase
{
    // RefreshDatabase: Ensuring that your tests run with a fresh database for each test method, providing a clean and consistent state for testing
    // WithFaker: Enabling Faker to generate fake data
    use RefreshDatabase, WithFaker;

    /**
     * Test index stories with unauthenticated user
     */
    public function testIndexStoriesUnauthenticated()
    {
        // Make a GET request to the index endpoint with unauthenticated user
        $response = $this->getJson('/api/stories');
        
         // Asserts that the response has a 401 status code
        $response->assertUnauthorized();
    }

    /**
     * Test index stories with authenticated user
     */
    public function testIndexStories()
    {
        // Create mock data
        $mockData = $this->createMockData();
      
        // Make a GET request to the index endpoint with authenticated user
        $response = $this->actingAs($mockData['user'], 'api')->getJson('/api/stories');

        // Assert that the response has status code 200 and expected structure
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'status',
                    ],
                ],
                'total',
            ]);
    }

    /**
     * Test show story
     */
    public function testShowStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Make a GET request to the show endpoint
        $response = $this->actingAs($mockData['user'], 'api')->getJson("/api/story/{$mockData['story']->id}");

        // Assert that the response has status code 200 and expected structure
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'content',
                ],
            ]);

        // Assert the response result
        $response->assertJson([
            'data' => [
                'id' => $mockData['story']->id,
                'title' => $mockData['story']->title,
                'content' => $mockData['story']->content,
            ],
        ]);
    }

    /**
     * Test show story with user who is not the owner of the story
     */
    public function testForbiddenShowStory()
    {
        // Create mock data
        $mockData = $this->createMockData(false);

        // Make a GET request to the show endpoint with user who is not the owner of the story
        $response = $this->actingAs($mockData['forbidden_user'], 'api')->getJson("/api/story/{$mockData['story']->id}");

        // Assert that the response has status code 403
        $response->assertForbidden();
    }

    /**
     * Test show story with user who is not the owner of the story, but the story is published, so user can access it
     */
    public function testForbiddenShowPublishedStory()
    {
        // Create mock data
        $mockData = $this->createMockData(true);

        // Make a GET request to the show endpoint with user who is not the owner of the story
        $response = $this->actingAs($mockData['forbidden_user'], 'api')->getJson("/api/story/{$mockData['story']->id}");

        // Assert that the response has status code 200 and expected structure
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'content',
                ],
            ]);

        // Assert the response result
        $response->assertJson([
            'data' => [
                'id' => $mockData['story']->id,
                'title' => $mockData['story']->title,
                'content' => $mockData['story']->content,
            ],
        ]);
    }

    /**
     * Test store story
     */
    public function testStoreStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Define the data to be sent in the request
        $data = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];

        // Use the token in the request header
        $response = $this->actingAs($mockData['user'], 'api')->postJson('/api/story', $data);

        // Assert that the response has status code 201 and expected structure
        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'content',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Assert the response result
        $response->assertJson([
            'data' => [
                'title' => $data['title'],
                'content' => $data['content'],
                // Default status value is 'draft'
                'status' => Story::STATUSES[0]
            ],
        ]);

        // Assert that data was created in the database
        $this->assertDatabaseHas('stories', [
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }

    /**
     * Test store story with failed validation
     */
    public function testFailedValidationStoreStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Define the data to be sent in the request
        $data = [
            'title' => $this->faker->sentence,
            // Content is required field, so it will fail validation
        ];

        // Use the token in the request header
        $response = $this->actingAs($mockData['user'], 'api')->postJson('/api/story', $data);

        // Assert that the response has status code 400
        $response->assertBadRequest();
    }

    /**
     * Test update story
     */
    public function testUpdateStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Updated data
        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'status' => Story::STATUSES[1],
        ];

        // Send the update request
        $response = $this->actingAs($mockData['user'], 'api')->patchJson("/api/story/{$mockData['story']->id}", $updatedData);

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert the response result
        $response->assertJson([
            'data' => [
                'title' => 'Updated Title',
                'content' => 'Updated Content',
                'status' => Story::STATUSES[1],
            ],
        ]);

        // Assert that data was updated in the database
        $this->assertDatabaseHas('stories', [
            'id' => $mockData['story']->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'status' => Story::STATUSES[1],
        ]);
    }

    /**
     * Test update story with user who is not the owner of the story
     */
    public function testForbiddenUpdateStory()
    {
       // Create mock data
       $mockData = $this->createMockData();

        // Updated data
        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'status' => Story::STATUSES[1],
        ];

        // Send the update request
        $response = $this->actingAs($mockData['forbidden_user'], 'api')->patchJson("/api/story/{$mockData['story']->id}", $updatedData);

        // Assert that the response has status code 403
        $response->assertForbidden();
    }

    /**
     * Test update story with failed validation
     */
    public function testFailedValidationUpdateStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Define the data to be sent in the request
        $data = [
            'title' => '', // Title should not be nullable, so it will fail validation
        ];

        // Use the token in the request header
        $response = $this->actingAs($mockData['user'], 'api')->postJson('/api/story', $data);

        // Assert that the response has status code 400
        $response->assertBadRequest();
    }

    /**
     * Test destroy story
     */
    public function testDestroyStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Send the delete request
        $response = $this->actingAs($mockData['user'], 'api')->deleteJson("/api/story/{$mockData['story']->id}");

        // Assert that the response has status code 200
        $response->assertStatus(200);

        /// Assert the response result
        $response->assertJson([
            'message' => 'Story is deleted successfully',
        ]);
    }

    /**
     * Test destroy story with user who is not the owner of the story
     */
    public function testForbiddenDestroyStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Send the delete request
        $response = $this->actingAs($mockData['forbidden_user'], 'api')->deleteJson("/api/story/{$mockData['story']->id}");

        // Assert that the response has status code 403
        $response->assertForbidden();
    }

    /**
     * Test delete story that does not exist
     */
    public function testNotFoundDestroyStory()
    {
        // Create mock data
        $mockData = $this->createMockData();

        // Use the token in the request header
        $response = $this->actingAs($mockData['user'], 'api')->deleteJson("/api/story/{99}");

        // Assert that the response has status code 404
        $response->assertNotFound();
    }

    /**
     * Create mock data for the tests
     */
    private function createMockData($isPublished = false)
    {
        // Create user
        $user = User::factory()->create();

        // Create user that is forbidden to access the story
        $forbiddenUser = User::factory()->create();

        // Create a story and associate it with the user
        $story = Story::factory()
            ->hasAttached($user)
            ->create([
                'status' => !$isPublished ? Story::STATUSES[0] : Story::STATUSES[1],
            ]);

        return [
            'user' => $user,
            'forbidden_user' => $forbiddenUser,
            'story' => $story,
        ];
    }
}
