<?php
namespace App\Services;

use App\Services\Traits\RemoteRequest;
use App\Data\Repositories\Users as UsersRepository;

class Authorization
{
    const PERMISSIONS_URL = 'https://apiportal.alerj.rj.gov.br/api/v1.0/adm-user/K7k8H95loFpTH0ZTRKX2BhADIusjXHInHW3cspyosOoNrbd5jOG3pd61F4d6fg584Gg5h4DSjui1k/permissions';
    const PROFILES_URL = 'https://apiportal.alerj.rj.gov.br/api/v1.0/adm-user/K7k8H95loFpTH0ZTRKX2BhADIusjXHInHW3cspyosOoNrbd5jOG3pd61F4d6fg584Gg5h4DSjui1k/profiles';
    const SYSTEM_NAME = 'Eventos';

    /**
     * @var RemoteRequest
     */
    private $remoteRequest;

    /**
     * Authorization constructor.
     *
     * @param RemoteRequest $remoteRequest
     */
    public function __construct(RemoteRequest $remoteRequest)
    {
        $this->remoteRequest = $remoteRequest;
    }
    /**
     * @param $username
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUserPermissions($username)
    {
        if (config('auth.authorization.mock')) {
            return $this->mockedPermissions($username);
        }

        try {
            $response = collect(
                $this->remoteRequest->post(static::PERMISSIONS_URL, [
                    'username' => $username,
                    'system' => static::SYSTEM_NAME,
                ])
            );
            return $response;
        } catch (\Exception $exception) {
            //Logando com as permissões salvas
            $usersRepository = app(UsersRepository::class);
            $user = $usersRepository->findByUsername($username);

            return $this->storedPermissions($user);
        }
    }

    /**
     * @param $username
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUserProfiles($username)
    {
        try {
            $response = collect(
                $this->remoteRequest->post(static::PROFILES_URL, [
                    'username' => $username,
                    'system' => static::SYSTEM_NAME,
                ])
            );
            return $response;
        } catch (\Exception $exception) {
            //Logando com os perfis salvos
            $usersRepository = app(UsersRepository::class);
            $user = $usersRepository->findByUsername($username);

            return $this->storedProfiles($user);
        }
    }

    /**
     * @param $user \App\Data\Models\User
     *
     * @return \Illuminate\Support\Collection
     */
    private function storedPermissions($user)
    {
        $permissionsArray = [];

        foreach ($user->all_permissions_array as $key => $item) {
            $permissionsArray[] = collect([
                'nomeFuncao' => $key,
                'evento' => $item,
            ]);
        }

        return collect($permissionsArray);
    }

    /**
     * @param $user \App\Data\Models\User
     *
     * @return \Illuminate\Support\Collection
     */
    private function storedProfiles($user)
    {
        $profilesArray = [];

        foreach ($user->all_profiles_array as $key => $item) {
            $profilesArray[] = collect([
                'NOME_PERFIL' => $key,
                'TIPO_PERFIL' => $item,
            ]);
        }

        return collect($profilesArray);
    }

    /**
     * @param $username
     *
     * @return \Illuminate\Support\Collection
     */
    public function mockedProfiles($username)
    {
        return collect(['Administrador' => 'S']);
    }

    /**
     * @param $username
     *
     * @return \Illuminate\Support\Collection
     */
    private function mockedPermissions($username)
    {
        return collect(['Editar']);
    }
}
