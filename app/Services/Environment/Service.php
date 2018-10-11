<?php

namespace App\Services\Environment;

use PragmaRX\Version\Package\Facade as Version;

class Service
{
    protected function buildEntitiesArray($user)
    {
        return $user ? collect([$user])->merge($user->businesses)->map(function ($entity) {
            $entity->class = get_class($entity);

            return $entity;
        }) : [];
    }

    public function data()
    {
        return coollect([
            'app' => [
                'name' => config('app.name'),
                'key' =>
                    auth()->user()
                        ? auth()->user()->client_key
                        : config('app.client_key'),
                'id' => auth()->user() ? auth()->user()->email : csrf_token(),
                'version' => Version::format('compact'),
            ],

            'token' => csrf_token(),

            'captchaEnabled' =>
                app()->environment() == 'local' && env('DISABLE_CAPTCHA')
                    ? false
                    : true,

            'captchaSiteKey' => config('services.recaptcha.siteKey'),

            'debug' => config('app.debug'),

            'session' => [
                'lifetime' => config('session.lifetime'),

                'frontend' => [
                    'lifetime' => config('session.frontend.lifetime'),

                    'warning' => config('session.frontend.warning'),
                ],
            ],

            'user' => auth()->user(),

            'timestamp' => now()->toDateTimeString(),

            'old' => coollect(old())
                ->except('password', 'password_confirmation')
                ->toArray(),
        ]);
    }
}