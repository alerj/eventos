<?php

namespace Tests\Browser;

use App\Data\Models\Category;
use DatabaseSeeder;
use App\Data\Repositories\Users as UsersRepository;
//use App\Data\Models\Category as CategoryModel;
use function PHPSTORM_META\type;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreatNewCategoryTest extends DuskTestCase
{
    //    /**
    //     * A basic browser test example.
    //     *
    //     * @return void
    //     */

    public function testCriarCategoria()
    {
        $this->browse(function (Browser $browser) {
            $user = app(UsersRepository::class)->randomElement()->username;
            $this->browse(function (Browser $browser) use ($user) {
                //                $category=factory(CategoryModel::class)->raw();
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
                    ->waitForText('Importar')
                    ->pause('7000')
                    ->click('@categories-option')
                    //                    ->waitForText('#')
                    ->pause('5000')
                    ->click('@create-category-button')
                    ->waitForText('Nova Categoria')
                    ->type('@category-name', 'nova categoria')
                    //                        $category['name'])
                    ->click('@record-category-button')
                    ->waitForText('#')
                    ->waitForText('nova categoria');
                    //                    ->waitForText($category['name'])
//                    ->screenshot('teste de Criar Categoria');
            });
        });
    }
}
