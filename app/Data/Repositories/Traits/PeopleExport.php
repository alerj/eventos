<?php

namespace App\Data\Repositories\Traits;

use App\Services\CSV;
use Illuminate\Support\Str;
use App\Services\PDF\Service as PDF;
use App\Data\Models\PersonInstitution;
use App\Data\Repositories\ExportableData;

trait PeopleExport
{
    protected $allContactTypes = [];

    protected $fieldTypes = [
        'name' => '01_',
        'nickname' => '02_',
        'birthdate' => '03_',
        'title' => '04_',
        'cpf' => '05_',
        'notes' => '06_',
        'party' => '07_',
        'institution' => '08_',
        'institution_title' => '09_',
        'role' => '10_',
        'contact' => '11_',
        'address' => '12_',
        'advisor' => '13_'
    ];

    public function export()
    {
        return request()->get('fileType') === 'pdf'
            ? $this->pdf()
            : $this->csv();
    }

    public function exportAll()
    {
        return $this->csvAll();
    }

    /**
     * @return array
     */
    protected function exportGetSelectList(): array
    {
        return [
            'people.id',
            'people.name as person_name',
            'people.nickname as person_nickname',
            'person_institutions.title as person_title',
            'people.birthdate as person_birthdate',
            'people.cpf as person_cpf',
            'people.notes as person_notes',
            'institutions.name as institution_name',
            'contacts.contact as contact',
            'contact_types.name as contact_type_name',
            'contact_types.code as contact_type_code'
        ];
    }

    /**
     * @return mixed
     */
    protected function exportQueryAndJoins()
    {
        return $this->newQuery()
            ->leftJoin(
                'person_institutions',
                'people.id',
                '=',
                'person_institutions.person_id'
            )
            ->leftJoin(
                'institutions',
                'person_institutions.institution_id',
                '=',
                'institutions.id'
            )
            ->leftJoin(
                'person_institutions as advisors',
                'advisors.advised_id',
                '=',
                'person_institutions.id'
            )
            ->leftJoin(
                'contacts',
                'contacts.person_institution_id',
                '=',
                'person_institutions.id'
            )
            ->leftJoin(
                'contact_types',
                'contacts.contact_type_id',
                '=',
                'contact_types.id'
            );
    }

    public function pdf()
    {
        return app(PDF::class)
            ->initialize($this->generateHtml())
            ->download(make_filename('pessoas', 'pdf'));
    }

    public function csv()
    {
        $fileName = tempnam(sys_get_temp_dir(), 'exporter_');

        file_put_contents(
            $fileName,
            $this->exportToCsv($this->getExportableData())
        );

        return response()
            ->download($fileName, make_filename('pessoas', 'csv'))
            ->deleteFileAfterSend(true);
    }

    public function csvAll()
    {
        $fileName = tempnam(sys_get_temp_dir(), 'exporter_');

        file_put_contents(
            $fileName,
            $this->exportToCsv($this->getAllExportableData())
        );

        return response()
            ->download($fileName, make_filename('pessoas', 'csv'))
            ->deleteFileAfterSend(true);
    }

    public function generateHtml()
    {
        return view('pdf.people')
            ->with([
                'heading' => 'Mailing',
                'subHeading' => '',
                'date' => now()->format('m/d/Y H:i'),
                'people' => $this->getExportableData()
            ])
            ->render();
    }

    public function exportToCsv($data)
    {
        return app(CSV::class)->generate($data);
    }

    public function exportFilterPeople()
    {
        set_time_limit(0);

        $query = $this->exportQueryAndJoins()->select(
            $this->exportGetSelectList()
        );

        $this->filterText($this->getQueryFilter(), $query);

        return $this->disablePagination()
            ->order($query)
            ->get();
    }

    public function getExportableData()
    {
        return collect($this->exportFilterPeople())
            ->groupBy('id')
            ->map(function ($person) {
                return $this->exportFlattenPerson($person);
            })
            ->map(function ($person) {
                $this->getExportableColumns()
                    ->pluck('alias')
                    ->each(function ($alias) use (&$person) {
                        if (!isset($person[$alias])) {
                            $person[$alias] = null;
                        }
                    });

                return $person;
            })
            ->map(function ($person) {
                return collect($person)->only(
                    $this->getExportableColumns()->pluck('alias')
                );
            })
            ->values()
            ->toArray();
    }

    public function getAllExportableData()
    {
        return $this->createExportableData(
            PersonInstitution::with('contacts')->get()
        )->toArray();
    }

    public function getExportableColumns()
    {
        return app(ExportableData::class)
            ->generatable()
            ->whereIn('code', request()->get('options'));
    }

    protected function exportFlattenPerson($person): array
    {
        $person = $person->map(function ($contact) {
            $contact['contact_type_' . $contact['contact_type_code']] =
                $contact['contact'];

            unset($contact['contact']);
            unset($contact['contact_type_code']);
            unset($contact['contact_type_name']);

            return $contact;
        });

        $item = [];

        $person->each(function ($columns) use (&$item) {
            collect($columns)->each(function ($column, $name) use (&$item) {
                $item[$name] = $column;
            });
        });

        return $item;
    }

    private function createExportableData($data)
    {
        return $data
            ->map(function ($item) {
                $result =
                    [
                        $this->fieldTypes['name'] . 'nome' => $item->person
                            ->name,

                        $this->fieldTypes['nickname'] . 'apelido' => $item
                            ->person->nickname,

                        $this->fieldTypes['birthdate'] . 'nascimento' => $item
                            ->person->birthdate,

                        $this->fieldTypes['title'] . 'tratamento' => $item
                            ->person->title,

                        $this->fieldTypes['cpf'] . 'cpf' => $item->person->cpf,

                        $this->fieldTypes['notes'] . 'observacoes' => $item
                            ->person->notes,

                        $this->fieldTypes['party'] . 'partido' =>
                            $item->person->party->name ?? null,

                        $this->fieldTypes['institution'] .
                        'instituicao' => $item->institution->name,

                        $this->fieldTypes['institution_title'] .
                        'instituicao_tratamento' => $item->institution->title,

                        $this->fieldTypes['role'] . 'cargo' => $item->role->name
                    ] +
                    $this->makeContactsArray($item->contacts) +
                    $this->makeAddressesArray($item->addresses) +
                    $this->makeAdvisorsArray($item->advisors);

                $this->allContactTypes = collect($this->allContactTypes)
                    ->merge(collect($result)->keys())
                    ->unique();

                return $result;
            })
            ->map(function ($item) {
                return $this->allContactTypes
                    ->sort()
                    ->mapWithKeys(function ($column) use ($item) {
                        return [
                            $this->removeFieldType($column) =>
                                $item[$column] ?? null
                        ];
                    });
            });
    }

    public function removeFieldType($column)
    {
        $column = substr($column, strlen($this->fieldTypes['name']));

        if (($pos = strpos($column, 'XY')) !== false) {
            $column = substr($column, 0, $pos) . substr($column, $pos + 4);
        }

        return $column;
    }

    public function makeContactsArray($contacts)
    {
        $types = [];

        $contacts = $contacts
            ->map(function ($contact) {
                return [
                    'type' => Str::slug($contact->contactType->name),
                    'contact' => $contact->contact
                ];
            })
            ->sortBy('type')
            ->mapWithKeys(function ($contact) use (&$types) {
                $types[$contact['type']] = isset($types[$contact['type']])
                    ? $types[$contact['type']] + 1
                    : 1;

                $contact['type'] = $contact['type'] . $types[$contact['type']];

                return [
                    $this->fieldTypes['contact'] . $contact['type'] => $contact[
                        'contact'
                    ]
                ];
            });

        return $contacts->toArray();
    }

    public function makeAddressesArray($addresses)
    {
        return $addresses
            ->map(function ($address) {
                return collect([
                    'XY1_cep' => $address->zipcode,
                    'XY2_endereco' => $address->street,
                    'XY3_numero' => $address->number,
                    'XY4_complemento' => $address->complement,
                    'XY5_bairro' => $address->neighbourhood,
                    'XY6_cidade' => $address->city,
                    'XY7_uf' => $address->state
                ]);
            })
            ->mapWithKeys(function ($address) use (&$types) {
                $types['endereco'] = isset($types['endereco'])
                    ? $types['endereco'] + 1
                    : 1;

                $address = $address->mapWithKeys(function ($value, $key) use (
                    $types
                ) {
                    return [
                        $this->fieldTypes['address'] .
                        'endereco' .
                        $types['endereco'] .
                        '_' .
                        $key => $value
                    ];
                });

                return $address;
            })
            ->toArray();
    }

    public function makeAdvisorsArray($advisors)
    {
        $types = [];

        return $advisors
            ->map(function ($advisor) {
                return collect(
                    [
                        $this->fieldTypes['name'] . 'nome' => $advisor->person
                            ->name,

                        $this->fieldTypes['role'] . 'cargo' => $advisor->role
                            ->name
                    ] +
                        $this->makeContactsArray($advisor->contacts) +
                        $this->makeAddressesArray($advisor->addresses)
                );
            })
            ->mapWithKeys(function ($advisor) use (&$types) {
                $order['assessor'] = isset($order['assessor'])
                    ? $order['assessor'] + 1
                    : 1;

                $advisor = $advisor->mapWithKeys(function ($value, $key) use (
                    $order
                ) {
                    return [
                        $this->fieldTypes['advisor'] .
                        'assessor' .
                        $order['assessor'] .
                        '_' .
                        $key => $value
                    ];
                });

                return $advisor;
            })
            ->toArray();
    }
}
