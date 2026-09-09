<?php

namespace App\Helpers;

use App\Models\Outlet;

/**
 * @property Outlet $cached
 * @property Outlet $change
 *
 * @method Outlet|null get()
 */
class SelectedOutlet
{
    private $user;

    public function __construct()
    {
        $this->user = request()->user();
    }

    // static factory
    public static function make(): self
    {
        return new self;
    }

    public function cached()
    {
        $sessionKey = $this->getSessionKey();

        if (session()->has($sessionKey)) {
            return session()->get($sessionKey);
        }

        if ($this->user && $this->user->outlets()->count() === 1) {
            $outlet = $this->user->business->outlets()->first();
            session()->put($sessionKey, $outlet);

            return $outlet;
        }

        return null;
    }

    public function change($outlet_id)
    {
        $outlet = Outlet::find($outlet_id);
        session()->put($this->getSessionKey(), $outlet);

        return $outlet;
    }

    public function get()
    {
        return session()->get($this->getSessionKey(), null);
    }

    public function all()
    {
        session()->forget($this->getSessionKey());

        return null;
    }

    private function getSessionKey()
    {
        return 'selected_outlet_'.($this->user ? $this->user->id : 'guest');
    }
}
