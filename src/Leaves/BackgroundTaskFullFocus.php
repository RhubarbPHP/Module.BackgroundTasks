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

namespace Rhubarb\Scaffolds\BackgroundTasks\Leaves;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Events\Event;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;

class BackgroundTaskFullFocus extends BackgroundTask
{
    private $acceptableWaitTime = false;

    public $taskCompleteEvent;

    public function __construct()
    {
        parent::__construct();

        $this->taskCompleteEvent = new Event();
    }

    protected function getViewClass()
    {
        return BackgroundTaskFullFocusView::class;
    }

    protected function processStartupEvents()
    {
        parent::processStartupEvents();

        $context = Application::current()->context();

        if (!$context->isXhrRequest()) {
            if ($this->acceptableWaitTime) {

                usleep($this->acceptableWaitTime);

                $status = new BackgroundTaskStatus($this->model->backgroundTaskStatusId);
                $status->reload();

                if (!$status->isRunning()) {
                    $this->taskCompleteEvent->raise($status->UniqueIdentifier);
                }
            }
        }
    }

    /**
     * An optional delay to pause for before displaying.
     *
     * If the task completes within this period, the TaskComplete event will be thrown immediately.
     *
     * @param $microseconds
     */
    public function setAcceptableWaitTime($microseconds)
    {
        $this->acceptableWaitTime = $microseconds;
    }
}
