<?php

namespace App\Traits;

trait SequencialNumberTrait
{
    public function sequencialID($model)
    {
        $id = $model::orderBy('id', 'desc')->withTrashed()->first()['id'] ?? 0;
        return $id + 1 . '/' .date('Y');
    }
}
