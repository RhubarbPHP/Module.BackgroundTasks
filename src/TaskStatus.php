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
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;

/**
 * A simple object to represent task status.
 */
class TaskStatus
{
    /**
     * @var double The percentage complete
     */
    public $percentageComplete;

    /**
     * @var string A message detailing the current status
     */
    public $message;

    /**
     * @var string The current status of the task. Using percent = 100 is not a reliable indicator of
     *             status as rounding might cause several progress reports of 100% before the task has
     *             finished.
     */
    public $status = BackgroundTaskStatus::TASK_STATUS_RUNNING;

    public function __construct($percentageComplete = "", $message = "", $status = null )
    {
        $this->percentageComplete = $percentageComplete;
        $this->message = $message;

        if ($status) {
            $this->status = $status;
        }
    }


}