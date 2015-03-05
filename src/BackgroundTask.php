<?php

namespace Rhubarb\BackgroundTasks;

use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;

abstract class BackgroundTask
{
    /**
     * Executes the long running code.
     *
     * @return void
     */
    public abstract function execute(BackgroundTaskStatus $status);

    /**
     * Initiates execution of the background task.
     *
     * @return BackgroundTaskStatus The status object for this task.
     */
    public static function initiate()
    {
        // Create an entry in our database.
        $task = new BackgroundTaskStatus();
        $task->TaskClass = get_called_class();
        $task->save();

        $command = "/usr/bin/php ".realpath( "vendor/rhubarbphp/rhubarb/platform/execute-cli.php" )." ".realpath( __DIR__."/Scripts/task-runner.php" )." ".$task->BackgroundTaskStatusID." > /dev/null 2>&1 &";

        $response = exec( $command );

        return $task;
    }
}