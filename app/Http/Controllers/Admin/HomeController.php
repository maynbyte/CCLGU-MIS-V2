<?php

namespace App\Http\Controllers\Admin;

use LaravelDaily\LaravelCharts\Classes\LaravelChart;

class HomeController
{
    public function index()
    {
        $settings1 = [
            'chart_title'           => 'Financial Assistance',
            'chart_type'            => 'latest_entries',
            'report_type'           => 'group_by_date',
            'model'                 => 'App\Models\FinancialAssistance',
            'group_by_field'        => 'date_interviewed',
            'group_by_period'       => 'day',
            'aggregate_function'    => 'count',
            'filter_field'          => 'created_at',
            'filter_period'         => 'month',
            'group_by_field_format' => 'Y-m-d H:i:s',
            'column_class'          => 'col-md-4',
            'entries_number'        => '20',
            'fields'                => [
                'user'         => '',
                'scheduled_fa' => '',
                'status'       => '',
            ],
            'translation_key' => 'financialAssistance',
        ];

        $settings1['data'] = [];
        if (class_exists($settings1['model'])) {
            $settings1['data'] = $settings1['model']::latest()
                ->take($settings1['entries_number'])
                ->get();
        }

        if (! array_key_exists('fields', $settings1)) {
            $settings1['fields'] = [];
        }

        $settings2 = [
            'chart_title'           => 'Total FA',
            'chart_type'            => 'line',
            'report_type'           => 'group_by_date',
            'model'                 => 'App\Models\FinancialAssistance',
            'group_by_field'        => 'created_at',
            'group_by_period'       => 'day',
            'aggregate_function'    => 'count',
            'filter_field'          => 'created_at',
            'filter_period'         => 'year',
            'group_by_field_format' => 'Y-m-d H:i:s',
            'column_class'          => 'col-md-12',
            'entries_number'        => '5',
            'translation_key'       => 'financialAssistance',
        ];

        $chart2 = new LaravelChart($settings2);

        return view('home', compact('chart2', 'settings1'));
    }
}
