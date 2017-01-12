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

use Rhubarb\Crown\Logging\Log;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Crown\Application;

/**
 * Extend this class to create an executable BackgroundTask
 */
abstract class BackgroundTask
{
    /**
     * @var array If sent, shell arguments will be populated here when execute is ran.
     */
    private static $shellArguments = [];

    /**
     * Executes the long running code.
     *
     * @param BackgroundTaskStatus $status
     * @return void
     */
    abstract public function execute(BackgroundTaskStatus $status);

    /**
     * If you need to provide additional arguments for the task runner (such as passing encrypted conenction
     * details) you should return the arguments as an array here.
     *
     * @return array
     */
    protected static function getAdditionalTaskRunnerArguments()
    {
        return [];
    }

    /**
     * Sets the shell arguments from the task-runner.php script
     *
     * @param $rawShellArguments
     */
    public static function setShellArguments($rawShellArguments)
    {
        self::$shellArguments = $rawShellArguments;
    }

    /**
     * Initiates execution of the background task.
     *
     * @param array $settings Settings which will be passed to the execute method of the BackgroundTask (must be JSON serialisable)
     *
     * @return BackgroundTaskStatus The status object for this task.
     */
    public static function initiate($settings = [])
    {
        // Create an entry in our database.
        $task = new BackgroundTaskStatus();
        $task->TaskClass = get_called_class();
        $task->TaskSettings = $settings;
        $task->save();

        $additionalArguments = static::getAdditionalTaskRunnerArguments();
        $additionalArgumentString = "";

        foreach ($additionalArguments as $argument) {
            $additionalArgumentString .= escapeshellarg($argument);
        }

        $runningRhubarbAppClass = escapeshellarg(get_class(Application::current()));
        $command = "rhubarb_app=$runningRhubarbAppClass /usr/bin/env php " . realpath(VENDOR_DIR . "/rhubarbphp/rhubarb/platform/execute-cli.php") . " " .
            realpath(__DIR__ . "/Scripts/task-runner.php") . " " . escapeshellarg(get_called_class()) . " " . $task->BackgroundTaskStatusID . " " . $additionalArgumentString . " > /dev/null 2>&1 &";

        Log::debug("Launching background task " . $task->UniqueIdentifier, "BACKGROUND", $command);

        exec($command);

        return $task;
    }
}
