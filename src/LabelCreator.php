<?php

namespace Tsung\NovaLabelCreator;

use Tsung\NovaLabelCreator\Nova\LabelType;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class LabelCreator extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('label-creator', __DIR__.'/../dist/js/tool.js');
        Nova::style('label-creator', __DIR__.'/../dist/css/tool.css');

        Nova::resources([
            LabelType::class,
        ]);
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('label-creator::navigation');
    }
}
