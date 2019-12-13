<?php

namespace Tests\Browser;

use DatabaseSeeder;
use App\Data\Repositories\Users as UsersRepository;
use App\Data\Models\Event as EventModel;
use function PHPSTORM_META\type;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreatEventTest extends DuskTestCase
{
    //    /**
    //     * A basic browser test example.
    //     *
    //     * @return void
    //     */

    public function testCriarEvento()
    {
        $this->artisan('migrate:fresh');
        $this->seed(DatabaseSeeder::class);
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $this->browse(function (Browser $browser) use ($user) {
                $event = factory(EventModel::class)->raw();
                $browser
                    ->loginAs(
                        app(UsersRepository::class)->findUserByEmail(
                            $user . '@alerj.rj.gov.br'
                        )
                    )
                    ->visit('/admin#')
                    ->click('@event-option')
                    ->pause('5000')
                    //                    ->waitForText('undefined')
                    ->click('@create-event-button')
                    //                    ->pause('8000')
                    ->waitForText('Criar Evento')
                    ->type('@event-name', $event['name'])
                    //                        app(EventsRepository::class)->randomElement()->name)
                    ->click('@record-event-button')
                    //                    ->pause('20000')
                    ->waitForText('Eventos (1)')
                    ->waitForText($event['name'])
                    //                    ->pause('4000')
                    ->assertSee($event['name']);
//                    ->screenshot('teste de Criar evento');
            });
        });
    }
}
