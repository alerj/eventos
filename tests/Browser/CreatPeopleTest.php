<?php

namespace Tests\Browser;

use App\Data\Models\ContactType;
use DatabaseSeeder;
use App\Data\Repositories\Users as UsersRepository;
use App\Data\Models\Person as PersonModel;
use App\Data\Repositories\Institutions as InstitutionsRepository;
use function PHPSTORM_META\type;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreatPeopleTest extends DuskTestCase
{
    //    /**
    //     * A basic browser test example.
    //     *
    //     * @return void
    //     */

    public function testCriarPessoa()
    {
        $this->browse(function (Browser $browser) {
//            $browser->screenshot('teste tela branca');
            $user = app(UsersRepository::class)->randomElement()->username;
            $person = factory(PersonModel::class)->raw();
            $this->browse(function (Browser $browser) use ($user, $person) {
                $browser
                    ->loginAs(
                        app(UsersRepository::class)->findUserByEmail(
                            $user . '@alerj.rj.gov.br'
                        )
                    )
                    ->visit('/admin#')
                    ->click('@people-option')
                    //                    ->waitForText('#')
                    ->pause('5000')
                    ->click('@create-people-button')
                    ->waitForText('Nova Pessoa')
                    //                    ->pause('2000')
                    ->type('@people-name', $person['name'])
                    ->click('@active-dusk')
                    ->type('@title', $person['title'])
                    //                    ->pause('5000')
                    ->click('@record-people-button')
                    ->waitForText('#')
                    ->waitForText($person['name'])
                    //                    ->pause('12000')
                    //                    ->click('@people-option')
                    //                    ->pause('8000')
                    //                    ->assertSee('zé')
                    ->assertSee($person['name']);

//                    ->screenshot('teste de pessoa');
            });
            //            $this ->assertDatabaseHas('')
        });
    }
    public function testAddFunction()
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
                    ->click('@people-option')
                    ->pause('10000')
                    ->waitForText('#')
                    ->click('@click-people')
                    //                    ->pause('5000')
                    ->waitForText('Categorias')
                    ->click('@add-function')
                    ->pause('5000')
                    ->waitForText('Instituição')
                    ->script([
                        "document.getElementById('institution_id').parentElement.__vue__.select(7)"
                    ]);
                //                    ->script(["document.getElementById('institution_id').parentElement.__vue__.select("+app(InstitutionsRepository::class)->randomElement()->id+")"]);
                $browser->script([
                    "document.getElementById('role_id').parentElement.__vue__.select(1)"
                ]);
                $browser
                    ->click('@active-check')
                    ->type('@title-name', 'Titulo')
                    ->click('@record-people-function')
                    ->pause('6000');
//                    ->screenshot('teste de função pessoa');
            });
        });
    }
    public function testAddCategory()
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
                    ->click('@people-option')
                    ->pause('13000')
                    //                    ->assertSee('Herlandson')
                    ->click('@click-people')
                    ->waitForText('Categorias')
                    ->click('@add-category')
                    ->waitForText('Associar Categorias')
                    //                    ->waitForText('nova categoria')
                    ->pause('5000')
                    ->click('@select-category')
                    ->click('@associate-category')
                    ->waitForText('Tratamento')
                    ->pause('7000');
                    //                    ->waitForText('nova categoria')
//                    ->screenshot('teste de categoria pessoa');
            });
        });
    }

    public function testAddContact()
    {
        $this->browse(function (Browser $browser) {
            $username = app(UsersRepository::class)->randomElement()->username;
            $user = app(UsersRepository::class)->findUserByEmail(
                $username . '@alerj.rj.gov.br'
            );

            \Auth::login($user);
            $emailId = ContactType::where('client_id', get_current_client_id())->where('name', 'E-mail')->first()->id;

            $this->browse(function (Browser $browser) use ($user, $emailId) {
                $browser
                    ->loginAs(
                        $user
                    )
                    ->visit('/admin#')
                    ->waitForText('Painel')
                    ->assertSee('Painel')
                    ->click('@people-option')
                    ->pause('12000')
                    ->click('@click-people')
                    ->waitForText('Categorias')
                    ->pause('5000')
                    ->click('@role-click')
                    ->waitForText('Contatos')
                    ->click('@add-contact')
                    ->waitForText('Criar Contato')
                    //                    ->click('Criar Contato');
                    ->script([
                        "document.getElementById('contact_type_id').parentElement.__vue__.select({$emailId})"
                    ]);
                $browser
//                    ->screenshot('teste email')
                    //                    ->click('@contact-email')
                    ->type('@contact-email', 'contato@abc.net')
                    ->click('@contact-active')
                    ->click('@record-contact-button')
                    ->waitForText('contato@abc.net')
                    ->pause('3000');
                    //                    ->assertSee('contato@abc.net')
//                    ->screenshot('teste contato da pessoa');
            });
        });
    }
}
