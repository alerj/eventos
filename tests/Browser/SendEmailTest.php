<?php

namespace Tests\Browser;

use DatabaseSeeder;
use App\Data\Repositories\Users as UsersRepository;
//use App\Data\Repositories\Events as EventsRepository;
use function PHPSTORM_META\type;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SendEmailTest extends DuskTestCase
{
    //    /**
    //     * A basic browser test example.
    //     *
    //     * @return void
    //     */

    public function testSendEmail()
    {
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $browser
                ->loginAs(
                    app(UsersRepository::class)->findUserByEmail(
                        $user . '@alerj.rj.gov.br'
                    )
                )
                ->visit('/admin#')
                ->waitForText('Painel')
                ->click('@event-option')
                ->pause('5000')
                ->click('@click-event')
                ->pause('5000')
                ->click('@sub-event')
                ->pause('5000')
                ->waitForText('Nome')
                ->click('@enviar-convite')
                ->waitForText('Deseja realmente enviar o convite', 10)
                ->press('sim')
                ->waitForText('enviados: 1', 10)
                ->assertSee('enviados: 1')
                ->click('@accept-invitation')
                ->waitForText('Deseja realmente confirmar', 10)
                ->press('sim')
                ->waitForText('aceito manualmente', 10)
                ->click('@send-credentials')
                ->waitForText('Deseja realmente enviar as credencias', 10)
                ->press('sim')
                ->waitForText('enviadas: 1', 10)
                ->assertSee('enviadas: 1');
            $this->browse(function (Browser $browser) use ($user) {
//                    ->screenshot('teste de Criar evento');
            });
        });
    }
}
