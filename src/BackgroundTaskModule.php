<?php

namespace Rhubarb\BackgroundTasks;

use Rhubarb\Crown\Module;
use Rhubarb\Stem\Schema\SolutionSchema;

class BackgroundTaskModule extends Module
{
    protected function initialise()
    {
        parent::initialise();

        SolutionSchema::registerSchema( "BackgroundTasks", __NAMESPACE__.'\Models\BackgroundTasksSolutionSchema' );
    }
}