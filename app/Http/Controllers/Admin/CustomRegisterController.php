<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\GuestGenerator\GuestUserDataGenerator;
use Backpack\CRUD\app\Http\Controllers\Auth\RegisterController;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cookie;

class CustomRegisterController extends RegisterController
{
    public function __construct(
        private readonly GuestUserDataGenerator $userDataGenerator,
        private readonly Redirector $redirector,
    ) {
        parent::__construct();
    }

    public function asGuest(Request $request): RedirectResponse
    {
        if (!config('backpack.base.registration_open')) {
            //always allow registration as guest
            //abort(403, trans('backpack::base.registration_closed'));
        }

        $request->request->add($this->userDataGenerator->get()->toArray());

        $this->validator($request->all())->validate();

        /** @var User $user */
        $user = $this->create($request->all());
        $user->setAsGuest();

        event(new Registered($user));
        if (config('backpack.base.setup_email_verification_routes')) {
            Cookie::queue('backpack_email_verification', $user->{config('backpack.base.email_column')}, 30);

            return $this->redirector->to(route('verification.notice'));
        }

        $this->guard()->login($user);

        return $this->redirector->to($this->redirectPath());
    }
}
