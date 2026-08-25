<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;

class NoAccessMessage extends Value
{
    public $name = 'Contact Your Administrator';

    public function calculate(NovaRequest $request): ValueResult
    {
        return $this->result('No Access')
            ->format('0,0')
            ->allowZeroResult();
    }

    public function ranges(): array
    {
        return [];
    }
}
