<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\DistributionEvent;

class AdminDistributionEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_quick_event_with_multiple_relief_types()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $start = now()->addHour();
        $end   = now()->addHours(2);

        $response = $this->actingAs($admin)->post(route('events.quick-store'), [
            'event_name'        => 'Test Event',
            'relief_type'       => ['Cash Aid', 'Food Pack'],
            'target_barangay'   => ['Sabang', 'Molino'],
            'started_at'        => $start->format('Y-m-d\TH:i'),
            'ended_at'          => $end->format('Y-m-d\TH:i'),
            'event_date'        => today()->format('Y-m-d'),
            'goods_detail'      => 'Some detail',
            'status'            => 'upcoming',
        ]);

        $response->assertRedirect(route('admin.distribution.logs'));

        $this->assertDatabaseHas('distribution_events', [
            'event_name'    => 'Test Event',
            // array should have been joined
            'relief_type'   => 'Cash Aid, Food Pack',
            'target_barangay' => 'Sabang, Molino',
        ]);
    }
}
