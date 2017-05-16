<?php
/**
 * Copyright (c) 2017 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Scaffolds\BackgroundTasks;

use Exception;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;

abstract class BackgroundTask extends Task
{
    /**
     * Executes a task and provisions a BackgroundTaskStatus model to store a persistent record of the state.
     * @param Task $task The task to execute
     * @param callable|null $progressReportedCallback An optional call back to receive progress events
     * @param BackgroundTaskStatus|null $persistentStatus If you have a pre-provisioned BackgroundTaskStatus model it can be updated instead.
     */
    public static function executeInBackground(Task $task, $progressReportedCallback = null, BackgroundTaskStatus $persistentStatus = null)
    {
        if (!$persistentStatus){
            $persistentStatus = new BackgroundTaskStatus();
            $persistentStatus->TaskClass = get_class($task);
            $persistentStatus->save();
        }

        $lastStatus = new TaskStatus();

        try {
            $task->execute(function(TaskStatus $status) use ($progressReportedCallback, &$lastStatus, &$persistentStatus){

                $lastStatus = $status;

                $persistentStatus->PercentageComplete = $status->percentageComplete;
                $persistentStatus->TaskStatus = $status->status;
                $persistentStatus->Message = $status->message;

                if ($progressReportedCallback){
                    $progressReportedCallback($status);
                }

                $persistentStatus->save();
            });

            $persistentStatus->TaskStatus = "Complete";
            $persistentStatus->save();

            if ($progressReportedCallback){
                // Output a final status of complete.
                $lastStatus->percentageComplete = 100;
                $lastStatus->status = BackgroundTaskStatus::TASK_STATUS_COMPLETE;
                $progressReportedCallback($lastStatus);
            }

        } catch (Exception $er) {
            $persistentStatus->TaskStatus = "Failed";
            $persistentStatus->ExceptionDetails = $er->getMessage() . "\r\n\r\n" . $er->getTraceAsString();
            $persistentStatus->save();

            if ($progressReportedCallback){
                // Output a final status of complete.
                $lastStatus->status = BackgroundTaskStatus::TASK_STATUS_FAILED;

                if ($er instanceof RhubarbException) {
                    $lastStatus->message = $er->getPublicMessage();
                }

                $progressReportedCallback($lastStatus);
            }
        }
    }
}