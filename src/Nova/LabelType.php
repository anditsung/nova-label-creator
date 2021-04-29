<?php

namespace Tsung\NovaLabelCreator\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class LabelType extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Tsung\NovaLabelCreator\Models\LabelType::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->creationRules('required', 'unique:label_creator_label_types,name')
                ->updateRules('required', 'unique:label_creator_label_types,name,{{resourceId}}'),

            BooleanGroup::make('Attributes')
                ->options([
                    'barcode' => 'Barcode',
                    'plant' => 'Plant',
                    'color' => 'Color',
                ])
                ->hideFromIndex(),

            Number::make('Number Digits')
                ->min(1)
                ->max(3)
                ->rules('required')
                ->hideFromIndex(),

            Select::make('Columns')
                ->options([
                    1 => 1,
                    2 => 2,
                    3 => 3
                ])
                ->rules('required')
                ->hideFromIndex(),

            Number::make('Break Count')
                ->min(1)
                ->rules('required')
                ->hideFromIndex(),

            Code::make('Design')
                ->language('php')
                ->rules('required'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
