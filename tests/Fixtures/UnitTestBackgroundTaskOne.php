<?php

namespace Rhubarb\BackgroundTasks\Tests\Fixtures;

use Rhubarb\BackgroundTasks\BackgroundTask;
use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;

class UnitTestBackgroundTaskOne extends BackgroundTask
{
    /**
     * Executes the long running code.
     *
     * @return void
     */
    public function execute(BackgroundTaskStatus $status)
    {
        touch( "cache/background-task-test.txt" );
    }
}