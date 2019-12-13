<?php

namespace Tests\Browser;

use DatabaseSeeder;
use App\Data\Repositories\Users as UsersRepository;
//use App\Data\Models\SubEvent as SubEventModel;
use function PHPSTORM_META\type;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreatSubEventTest extends DuskTestCase
{
    //    /**
    //     * A basic browser test example.
    //     *
    //     * @return void
    //     */

    public function testCriarSubEvento()
    {
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $this->browse(function (Browser $browser) use ($user) {
                //                $subEvent=factory(SubEventModel::class)->raw();
                $browser
                    ->loginAs(
                        app(UsersRepository::class)->findUserByEmail(
                            $user . '@alerj.rj.gov.br'
                        )
                    )
                    ->visit('/admin#')
                    ->waitForText('Painel')
                    ->assertSee('Painel')
                    ->click('@event-option')
                    ->pause('10000')
                    //                    ->waitForText('evento teste')
                    //                    ->assertSee('evento teste')
                    ->click('@click-event')
                    ->waitForText('Confirmado')
                    ->click('@create-sub-event')
                    ->pause('2000')
                    //                    ->waitForText('Novo Sub Evento')
                    ->type('@sub-name', 'sub teste')
                    ->keys('#date', '210219')
                    ->keys('#time', '00002315')
                    ->check('@send_invitations')
                    ->check('@send_credentials')
                    ->type('@sub-adress', 'local qualquer')
                    ->script([
                        "document.getElementById('sector_id').parentElement.__vue__.select(3)"
                    ]);
                $browser
                    ->click('@sub-record')
                    ->waitForText('Confirmado')
                    ->click('@click-event')
                    ->waitForText('sub teste');
                    //                    ->pause('7000')
                    //                    ->click(Confirmar Sub-evento)
//                    ->screenshot('teste criar sub evento');
            });
        });
    }
    public function testConvidarSubEvento()
    {
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $this->browse(function (Browser $browser) use ($user) {
                $browser
                    ->loginAs(
                        app(UsersRepository::class)->findUserByEmail(
                            $user . '@alerj.rj.gov.br'
                        )
                    )
                    ->visit('/admin#')
                    ->waitForText('Painel')
                    ->assertSee('Painel')
                    ->click('@event-option')
                    ->pause('8000')
                    //                    ->assertSee('evento teste')
                    ->click('@click-event')
                    ->waitForText('Confirmado')
                    ->waitForText('sub teste')
                    ->click('@sub-event')
                    ->waitForText('convidados')
                    ->assertSee('convidados')
                    ->click('@add-guest')
                    ->pause('5000')
                    //                    ->waitForText('Convidar Pessoas')
                    //                    ->waitForText('Herlandson')
                    ->click('@click-invite')
                    ->click('@invite-button')
                    ->click('@event-option')
                    //                    ->waitForText('Herlandson')
                    ->pause('5000');
//                    ->screenshot('teste convidar sub evento');
            });
        });
    }
    public function testConfirmarSubEvento()
    {
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $this->browse(function (Browser $browser) use ($user) {
                $browser
                    ->loginAs(
                        app(UsersRepository::class)->findUserByEmail(
                            $user . '@alerj.rj.gov.br'
                        )
                    )
                    ->visit('/admin#')
                    ->waitForText('Painel')
                    ->assertSee('Painel')
                    ->click('@event-option')
                    ->pause('20000')
                    //                    ->waitForText('evento teste')
                    //                    ->pause('5000')
                    ->click('@click-event')
                    ->waitForText('Confirmado')
                    ->waitForText('sub teste')
                    ->click('@confirm-subevent')
                    ->press('sim')
                    ->pause('5000');
//                    ->screenshot('teste confirmar sub evento');
            });
        });
    }
}
