<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Scaffolds\BackgroundTasks;

use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;

/**
 * Extend this class to create an executable BackgroundTask
 */
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
    public static function initiate( $settings = [] )
    {
        // Create an entry in our database.
        $task = new BackgroundTaskStatus();
        $task->TaskClass = get_called_class();
        $task->TaskSettings = $settings;
        $task->save();

        $command = "/usr/bin/php " . realpath("vendor/rhubarbphp/rhubarb/platform/execute-cli.php") . " " . realpath(__DIR__ . "/Scripts/task-runner.php") . " " . $task->BackgroundTaskStatusID . " > /dev/null 2>&1 &";

        exec($command);

        return $task;
    }
}