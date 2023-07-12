<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function subscribedUsers()
    {
        return User::whereNotNull('subscribed_at')->get();
    }

    public function getUsersTelegramId()
    {
        return User::where('telegram_Id', '!=', 0)->get();
    }

    public function getUserCurrencies(User $user)
    {
        return $user->currencies()->pluck('currency_id');
    }

    public function getById($id)
    {
        return User::find($id);
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function update($id, $data)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete($id)
    {
        return User::destroy($id);
    }
}
