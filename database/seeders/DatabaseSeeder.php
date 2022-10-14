<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $daLocalsKnow = Club::create(['name' => 'Da Locals Know']);
        $freshwaterFolk = Club::create(['name' => 'Freshwater Folk']);
        $theNorthernPikes = Club::create(['name' => 'The Northern Pikes']);
        $bassCatchersUnited = Club::create(['name' => 'Bass Catchers United']);

        $user = User::factory()->create([
            'name' => 'Lloyd Montgomery',
            'club_id' => $theNorthernPikes->id,
            'email' => 'lloyd@example.com',
        ])->buddies()->sync([
            User::factory()->create(['name' => 'Rusty Coleman', 'club_id' => $theNorthernPikes->id])->id,
            User::factory()->create(['name' => 'Jed Davenport', 'club_id' => $daLocalsKnow->id])->id,
            User::factory()->create(['name' => 'Jackson Lee', 'club_id' => $theNorthernPikes->id])->id,
            User::factory()->create(['name' => 'Zeb Stansfield', 'club_id' => $bassCatchersUnited->id])->id,
            User::factory()->create(['name' => 'Ottis Grayson', 'club_id' => $theNorthernPikes->id])->id,
            User::factory()->create(['name' => 'Bob Stafford', 'club_id' => $bassCatchersUnited->id])->id,
        ]);

        User::factory(993)->create();

        User::all()->each(function ($user) {
            $user->trips()->saveMany(Trip::factory(250)->make());
        });
    }
}
