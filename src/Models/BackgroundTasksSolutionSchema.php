<?php

namespace Rhubarb\BackgroundTasks\Models;

use Rhubarb\Stem\Schema\SolutionSchema;

class BackgroundTasksSolutionSchema extends SolutionSchema
{
    public function __construct()
    {
        parent::__construct(0.1);

        $this->addModel("BackgroundTask", __NAMESPACE__.'\BackgroundTask');
    }
}