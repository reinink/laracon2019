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

        User::factory(5999)->create();

        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Lloyd Montgomery',
            'club_id' => $theNorthernPikes->id,
            'email' => 'lloyd@example.com',
        ]);

        User::all()->each(function ($user) use (
            $daLocalsKnow,
            $freshwaterFolk,
            $theNorthernPikes,
            $bassCatchersUnited
        ) {
            $i = 0;
            while ($i <= 100) {
                $user->friends()->saveMany([
                    User::whereClubId($daLocalsKnow->id)->whereNotIn('id', $user->friends()->select('friend_id as id'))->inRandomOrder()->first(),
                    User::whereClubId($freshwaterFolk->id)->whereNotIn('id', $user->friends()->select('friend_id as id'))->inRandomOrder()->first(),
                    User::whereClubId($theNorthernPikes->id)->whereNotIn('id', $user->friends()->select('friend_id as id'))->inRandomOrder()->first(),
                    User::whereClubId($bassCatchersUnited->id)->whereNotIn('id', $user->friends()->select('friend_id as id'))->inRandomOrder()->first(),
                ]);
                $i++;
            }

        });

        User::all()->each(function ($user) {
            $user->trips()->saveMany(Trip::factory(250)->make());
        });
    }
}
