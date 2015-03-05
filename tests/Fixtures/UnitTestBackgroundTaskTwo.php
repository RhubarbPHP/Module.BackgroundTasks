<?php

namespace Rhubarb\BackgroundTasks\Tests\Fixtures;

use Rhubarb\BackgroundTasks\BackgroundTask;
use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;

class UnitTestBackgroundTaskTwo extends BackgroundTask
{
    /**
     * Executes the long running code.
     *
     * @return void
     */
    public function execute(BackgroundTaskStatus $status)
    {
        $status->Message = "Foo";
        $status->save();

        usleep(200000);
        $status->Message = "Bar";
        $status->save();

        touch( "cache/background-task-test.txt" );
    }
}