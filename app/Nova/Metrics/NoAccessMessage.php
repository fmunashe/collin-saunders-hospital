<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class NoAccessMessage extends Value
{
    public $name = 'Access Restricted';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->result(0)
            ->allowZeroResult()
            ->prefix('⚠️')
            ->suffix('Please contact your administrator to be assigned a role and permissions.');
    }

    public function ranges(): array
    {
        return [];
    }
}
