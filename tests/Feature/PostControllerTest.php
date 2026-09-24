<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_listing_posts(): void
    {
        $response = $this->get(route('posts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_post_list_with_author_names(): void
    {
        $author = User::factory()->create(['name' => 'Ada Lovelace']);
        Post::factory()->for($author, 'author')->create(['title' => 'First post']);

        $response = $this->actingAs(User::factory()->create())->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee('First post');
        $response->assertSee('Ada Lovelace');
    }

    public function test_creating_post_persists_authenticated_user_as_author(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'My new post',
            'content' => 'Some content.',
        ]);

        $post = Post::firstWhere('title', 'My new post');

        $response->assertRedirect(route('posts.show', $post));
        $this->assertSame($user->id, $post->author_id);
    }

    public function test_creating_post_ignores_client_supplied_author_id(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'My new post',
            'content' => 'Some content.',
            'author_id' => $otherUser->id,
        ]);

        $post = Post::firstWhere('title', 'My new post');

        $this->assertSame($user->id, $post->author_id);
    }

    public function test_creating_post_requires_title_and_content(): void
    {
        $response = $this->actingAs(User::factory()->create())->post(route('posts.store'), [
            'title' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_author_can_update_their_own_post(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->for($author, 'author')->create();

        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Updated title',
            'content' => 'Updated content.',
        ]);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertSame('Updated title', $post->fresh()->title);
    }

    public function test_other_user_cannot_update_someone_elses_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs(User::factory()->create())->put(route('posts.update', $post), [
            'title' => 'Hijacked title',
            'content' => 'Hijacked content.',
        ]);

        $response->assertForbidden();
        $this->assertNotSame('Hijacked title', $post->fresh()->title);
    }

    public function test_author_can_delete_their_own_post(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->for($author, 'author')->create();

        $response = $this->actingAs($author)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertNull($post->fresh());
    }

    public function test_other_user_cannot_delete_someone_elses_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete(route('posts.destroy', $post));

        $response->assertForbidden();
        $this->assertNotNull($post->fresh());
    }

    public function test_admin_can_delete_someone_elses_post(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('ADMIN', 'web'));
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertNull($post->fresh());
    }
}
