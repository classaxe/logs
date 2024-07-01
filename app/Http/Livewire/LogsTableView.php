<?php

namespace App\Http\Livewire;

use App\Models\Log;
use LaravelViews\Views\TableView;

class LogsTableView extends TableView
{
    /**
     * Sets a model class to get the initial data
     */
    protected $model = Log::class;

    /**
     * Sets the headers of the table as you want to be displayed
     *
     * @return array<string> Array of headers
     */
    public function headers(): array
    {
        return [
            'Date',
            'UTC',
            'Call',
            'Band',
            'Mode',
            'RX',
            'TX',
            'Pwr',
            'QTH',
            'S/P',
            'ITU',
            'GSQ',
            'Km',
            'Conf'
        ];
    }

    /**
     * Sets the data to every cell of a single row
     *
     * @param $model
     */
    public function row($model): array
    {
        return [
            $model->date,
            $model->time,
            $model->call,
            $model->band,
            $model->mode,
            $model->rx,
            $model->tx,
            $model->pwr,
            $model->qth,
            $model->sp,
            $model->itu,
            $model->gsq,
            $model->km,
            $model->conf
        ];
    }
}
