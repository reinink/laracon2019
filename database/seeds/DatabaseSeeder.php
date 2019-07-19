<?php

use App\Club;
use App\Trip;
use App\User;
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

        $user = factory(User::class)->create([
            'name' => 'Lloyd Montgomery',
            'club_id' => $theNorthernPikes->id,
            'email' => 'lloyd@example.com',
        ])->buddies()->sync([
            factory(User::class)->create(['name' => 'Rusty Coleman', 'club_id' => $theNorthernPikes->id])->id,
            factory(User::class)->create(['name' => 'Jed Davenport', 'club_id' => $daLocalsKnow->id])->id,
            factory(User::class)->create(['name' => 'Jackson Lee', 'club_id' => $theNorthernPikes->id])->id,
            factory(User::class)->create(['name' => 'Zeb Stansfield', 'club_id' => $bassCatchersUnited->id])->id,
            factory(User::class)->create(['name' => 'Ottis Grayson', 'club_id' => $theNorthernPikes->id])->id,
            factory(User::class)->create(['name' => 'Bob Stafford', 'club_id' => $bassCatchersUnited->id])->id,
        ]);

        factory(User::class, 993)->create();

        User::all()->each(function ($user) {
            $user->trips()->saveMany(factory(Trip::class, 250)->make());
        });
    }
}
