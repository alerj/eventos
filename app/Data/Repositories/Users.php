<?php

namespace App\Data\Repositories;

use App\Data\Models\User;
use App\Services\Authorization;
use Illuminate\Support\Facades\Hash;

class Users extends Repository
{
    /**
     * @var string
     */
    protected $model = User::class;

    protected $tiposUsuarios;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * Users constructor.
     *
     * @param Authorization $authorization
     */
    public function __construct(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @param $request
     *
     * @return mixed
     */
    private function credentials($request)
    {
        return $request->only(['username', 'password']);
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findUserById($id)
    {
        return User::find($id);
    }

    /**
     * @param $request
     * @param $remember
     *
     * @return User
     */
    public function updateLoginUser($request, $remember)
    {
        try {
            $credentials = extract_credentials($request);

            if (
                is_null(
                    ($user = $this->findUserByEmail(
                        ($email = "{$credentials['username']}@alerj.rj.gov.br")
                    ))
                )
            ) {
                $user = new User();

                $user->name = $credentials['username'];

                $user->username = $credentials['username'];

                $user->email = $email;
            }

            $user->password = Hash::make($credentials['password']);

            $user->save();
        } catch (\Exception $exception) {
            report($exception);

            return null;
        }

        return $user;
    }

    /**
     * @param $permissions
     *
     * @return User
     */
    public function updatePermissions($user, $permissions)
    {
        $permissionsArray = [];
        $permissions->each(function ($permission, $key) use (
            &$permissionsArray
        ) {
            $permissionsArray[$permission['nomeFuncao']] =
                $permission['evento'];
        });

        $user->permissions = json_encode($permissionsArray);
        $user->save();

        return $user;
    }

    /**
     * @param $permissions
     *
     * @return User
     */
    public function updateProfiles($user, $profiles)
    {
        $profilesArray = [];

        $profiles->each(function ($profile, $key) use (&$profilesArray) {
            $profilesArray[$profile['NOME_PERFIL']] = $profile['TIPO_PERFIL'];
        });

        $user->profiles = json_encode($profilesArray);
        $user->save();

        return $user;
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public function findByType($type)
    {
        return $this->model
            ::where('user_type_id', $this->tiposUsuarios->findByName($type)->id)
            ->get();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $query = User::orderBy('name');

        if (!is_administrator()) {
            $administrator = $this->tiposUsuarios->findByName('Administrador');

            $query->where('user_type_id', '!=', $administrator->id);
        }

        return $query->get();
    }

    /**
     * @return mixed
     */
    public function notifiables()
    {
        return User::where('all_notifications', true)->get();
    }

    /**
     * @param $user
     * @param $permissionString
     * @return bool
     */
    public function userHasPermission($user, $permissionString)
    {
        foreach ($user->permissions_array as $key => $item) {
            if ($key == $permissionString) {
                return false;
            }
        }
        return false;
    }
}
