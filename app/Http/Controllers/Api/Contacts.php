<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Data\Repositories\Contacts as ContactsRepository;
use App\Http\Requests\ContactStore;
use App\Http\Requests\ContactUpdate;
use Illuminate\Http\Request;

class Contacts extends Controller
{
    /**
     * @param Request $request
     * @param $personInstitutionId
     * @return mixed
     */
    public function all(Request $request, $personInstitutionId)
    {
        return app(ContactsRepository::class)->allByPersonInstitutionId(
            $personInstitutionId
        );
    }

    /**
     * Store
     *
     * @param ContactStore $request
     * @return mixed
     */
    public function store(ContactStore $request)
    {
        return app(ContactsRepository::class)->storeFromArray($request->all());
    }

    /**
     * @param ContactUpdate $request
     * @param $id
     * @return mixed
     */
    public function update(ContactUpdate $request, $id)
    {
        return app(ContactsRepository::class)->update($id, $request->all());
    }
}
