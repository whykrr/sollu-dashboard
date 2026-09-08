<?php

namespace App\Observers;

use App\Helpers\SummaryUser;
use Illuminate\Database\Eloquent\Model;

class UserCacheObserver
{
    protected function clearUserCache(Model $model): void
    {
        if ($model instanceof \App\Models\User) {
            SummaryUser::cacheDelete($model->id);
            return;
        }

        if (method_exists($model, 'users')) {
            foreach ($model->users as $user) {
                SummaryUser::cacheDelete($user->id);
            }
        } elseif (method_exists($model, 'business') && $model->business) {
            foreach ($model->business->users as $user) {
                SummaryUser::cacheDelete($user->id);
            }
        }
    }

    public function saved(Model $model): void
    {
        $this->clearUserCache($model);
    }

    public function deleted(Model $model): void
    {
        $this->clearUserCache($model);
    }
}
