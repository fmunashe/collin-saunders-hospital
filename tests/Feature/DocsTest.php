<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_docs_portal_requires_authentication(): void
    {
        $this->get('/docs')->assertRedirect();
    }

    public function test_authenticated_user_sees_the_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/docs');

        $response->assertOk();
        $response->assertSee('Documentation Portal');
    }

    public function test_markdown_doc_is_rendered_as_html(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/docs/HMS_Team_Composition.md');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        // Markdown was converted (no raw hashes/pipes shown as headings) and themed.
        $response->assertSee('Back to Documentation Portal');
    }

    public function test_html_doc_is_served(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/docs/01_Solution_Design.html')->assertOk();
    }

    public function test_doc_pages_include_the_brand_favicon(): void
    {
        $user = User::factory()->create();

        // HTML doc
        $this->actingAs($user)->get('/docs/02_Architecture.html')
            ->assertOk()
            ->assertSee('/favicon.svg', false);

        // Markdown-rendered doc
        $this->actingAs($user)->get('/docs/HMS_Team_Composition.md')
            ->assertOk()
            ->assertSee('/favicon.svg', false);
    }

    public function test_restricted_costing_doc_is_not_served(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/docs/HMS_Project_Costing_Proposal.md')
            ->assertNotFound();
    }

    public function test_costing_doc_is_absent_from_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/docs')
            ->assertOk()
            ->assertDontSee('Project Costing Proposal');
    }

    public function test_path_traversal_is_blocked(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/docs/..%2F..%2F.env')->assertNotFound();
    }
}
