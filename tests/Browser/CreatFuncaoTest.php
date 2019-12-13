<?php

namespace Tests\Browser;

use App\Data\Models\Role;
use DatabaseSeeder;
use App\Data\Repositories\Users as UsersRepository;
use App\Data\Models\Role as RoleModel;
use function PHPSTORM_META\type;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreatFuncaoTest extends DuskTestCase
{
    //    /**
    //     * A basic browser test example.
    //     *
    //     * @return void
    //     */

    public function testCriarFuncao()
    {
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $this->browse(function (Browser $browser) use ($user) {
                $role = factory(RoleModel::class)->raw();
                $browser
                    ->loginAs(
                        app(UsersRepository::class)->findUserByEmail(
                            $user . '@alerj.rj.gov.br'
                        )
                    )
                    ->visit('/admin#')
                    ->waitForText('Painel')
                    ->assertSee('Painel')
                    ->click('@drop-menu')
                    ->pause('3000')
                    ->click('@function-option')
                    ->waitForText('#')
                    //                    ->pause('7000')
                    ->click('@create-roles-button')
                    ->waitForText('Nova Função')
                    ->type('@role-name', $role['name'])
                    ->click('@record-role-button')
                    ->pause('5000')
                    ->assertSee($role['name'])
                    ->waitForText('Funções');
//                    ->screenshot('teste de Criar funcao');
            });
        });
    }
}
