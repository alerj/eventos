<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Data\Models\Client;
use App\Data\Models\Sector;
use App\Services\CSV;
use App\Data\Models\ContactType;
use App\Data\Models\Institution;
use App\Data\Models\Costume as CostumeModel;
use App\Data\Repositories\Costumes as CostumesRepositories;

class CreateClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventos:create-client {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        dump('Creating client');
        $newClient = $this->createClient($name);
        dump('Client created');

        set_current_client_id($newClient->id);

        dump('Creating parties');
        $this->createParties();
        dump('Parties created');

        dump('Creating contact types');
        $this->createContactTypes();
        dump('Contact types created');

        dump('Creating costumes');
        $this->createCostumes();
        dump('Costumes created');

        dump('Creating sectors');
        $this->createSectors();
        dump('Sectors created');

        dump('Creating sector colors');
        $this->createSectorColors();
        dump('Sector colors created');
    }

    public function createClient($name)
    {
        $row = new Client();
        $row->name = $name;

        $row->settings = [
            'signature' => null,
            'mail_from_name'=> null,
            'mail_from_email'=> null,
        ];

        $row->save();

        return $row;
    }

    private function createContactTypes()
    {
        collect([
            [0, 'Celular', 'mobile'],
            [1, 'Whatsapp', 'whatsapp'],
            [2, 'E-mail', 'email'],
            [3, 'Telefone Fixo', 'phone'],
        ])->each(function ($item, $key) {
            $row = new ContactType();
            $row->name = $item[1];
            $row->code = $item[2];
            $row->save();
        });
    }

    private function createCostumes()
    {
        $insertArray = [
            'Esporte' => 'O traje esporte é o mais casual e livre. Aqui não há regras e entram materiais menos nobres, como o jeans, o algodão e a sarja.
O traje esporte é indicado para ocasiões mais informais e a escolha de peças é livre, como mostram Gwyneth Paltrow e Daniel Rocha. Fotos: AP e TV Globo
Feminino:
Vale tudo! Vestido, saia, calça, bermuda, blusa, bata... A maquiagem e o cabelo também podem fazer a linha mais relax, seguindo o gosto da pessoa.
Masculino:
Calça jeans ou de sarja, bermuda, camiseta, camisa polo, "sapatênis", sandália...',

            'Passeio, esporte fino' => 'Também chamado de tenue de ville ("roupa de cidade", em francês), esse tipo de traje deixa o casual um pouco mais formal e arrumado. Os materiais devem ser mais sofisticados, o salto pode aumentar e a bolsa, diminuir. Até as 18h, pode ser mais informal; depois deste horário, é bom apostar em uma dose maior de glamour.
Emma Watson e Cauã Reymond no estilo esporte fino: nem muito casual, nem exageradamente arrumado. Fotos: AP e TV Globo
Feminino:
O cocktail dress, um clássico das festas, é a melhor pedida neste caso. Simples, discreto e na altura do joelho, ele pode ser usado com paletós e casaquinhos. A calça também está liberada, mas em materiais mais sofisticados. A maquiagem também é mais elaborada, embora ainda discreta. As joias ou bijuterias não devem ser exageradamente glamourosas.
Masculino:
O homem deve usar terno neste caso. Antes das 18h, o uso da gravata é dispensável; depois deste horário, o uso do acessório é recomendável. É sempre bom atentar para os detalhes e lembrar de verificar se os sapatos estão engraxados, se o cinto está com boa aparência etc.',

            'Social, passeio completo ou uniforme correspondente' => 'O passeio completo é bem formal e fica apenas um passo atrás do traje de gala em termos de sofisticação.
Thalia e Tom Hanks. Fotos: AP
Feminino:
A mulher deve investir em looks mais elaborados com materiais e detalhes nobres, como seda, chiffon, bordados e brilhos. O ideal é apostar em uma joia poderosa ou em uma bijuteria que imite joia. O sapato deve ser de salto alto e a bolsa, em modelo clutch ou carteira. Se a festa tiver pista de dança, uma bolsinha de festa pequena com alça a tiracolo também é ótima pedida para dançar à vontade.
Masculino:
No traje passeio completo, o homem deve usar terno – de preferência escuro – e gravata. Tenha atenção redobrada! A roupa tem que estar bem passada e alinhada. Calças podem ter vincos; mangas de camisas e paletós, não. Se o homem tiver uma abotoadura bonita, esta é a hora de usá-la. O mesmo vale para um relógio clássico.',

            'A rigor, de gala ou black tie' => 'Também chamado de tenue de soirée ("roupa de noite", em francês), o traje de gala é destinado às festas superformais; no nível dos casamentos ou dos tapetes vermelhos, mesmo.
Sofia Vergara e George Clooney em trajes de gala. Fotos: AP
Feminino:
O vestido longo é o mais indicado, mas, na última década, os curtos chiques também passaram a ser aceitos. Os tecidos devem ser finos, com transparências, bordados, brilhos... Não economize nas joias e, se estiver frio, aposte em boleros e echarpes de tecidos nobres. O salto alto deve ser fino e mais alto, combinados a bolsas pequenas ou miniaudières. Capriche no penteado elaborado e escolha uma maquiagem digna de Hollywood.
Masculino:
No traje de gala, não tem negociação: o homem tem que usar smoking. O terno deve ser preto com a lapela do paletó e uma faixa fininha da lateral da calça em cetim. A gravata borboleta também é indispensável. A camisa é branca e o sapato, em verniz brilhante.',
        ];

        foreach ($insertArray as $key => $item) {
            $row = new CostumeModel();

            $row->name = $key;

            $row->description = $item;

            $row->save();
        }

        $costume = app(CostumesRepositories::class)->findByName(
            'Social, passeio completo ou uniforme correspondente'
        );

        $costume->name = 'Passeio completo ou uniforme correspondente';

        $costume->save();
    }

    protected function createParties(): void
    {
        (new CSV())
            ->parse(file_get_contents(database_path('data/parties.csv')))
            ->each(function ($party) {
                Institution::firstOrCreate([
                    'client_id' => Client::where('name', 'Cerimonial')->first()
                        ->id,
                    'name' => $party->name,
                    'initials' => $party->initials,
                    'party_number' => $party->number,
                    'is_party' => true,
                ]);

                Institution::firstOrCreate([
                    'client_id' => Client::where('name', 'Forum')->first()->id,
                    'name' => $party->name,
                    'initials' => $party->initials,
                    'party_number' => $party->number,
                    'is_party' => true,
                ]);
            });
    }

    private function createSectorColors()
    {
        foreach (
            [
                ['name' => 'Galeria', 'color' => '#455D9A'],
                ['name' => 'Tribuna de Honra', 'color' => '#597532'],
                ['name' => 'Tribuna Especial', 'color' => '#E8BC28'],
                ['name' => 'Plenário', 'color' => '#A3E600'],
                ['name' => 'Cerimonial', 'color' => '#B39963'],
                ['name' => 'Imprensa', 'color' => '#C23334'],
                ['name' => 'Segurança', 'color' => '#000000'],

                ['name' => 'Estacionamento', 'color' => '#E65C21'],
                ['name' => 'Funcionarios', 'color' => '#A19E9C'],
            ]
            as $item
        ) {
            Sector::updateOrCreate(
                ['name' => $item['name']],
                ['color' => $item['color']]
            );
        }
    }

    private function createSectors()
    {
        foreach (
            [
                ['name' => 'Galeria', 'color' => '#739735'],
                ['name' => 'Tribuna de Honra', 'color' => '#997220'],
                ['name' => 'Plenário', 'color' => '#224792'],
                ['name' => 'Estacionamento', 'color' => '#000000'],
            ]
            as $item
        ) {
            Sector::create($item);
        }
    }
}
