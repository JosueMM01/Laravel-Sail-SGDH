<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        // Antes de crear un registro
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->last_modified_by_user_id = Auth::id();
            }
        });

        // Antes de actualizar un registro
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->last_modified_by_user_id = Auth::id();
            }
        });
    }
}