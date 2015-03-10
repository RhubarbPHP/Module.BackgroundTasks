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

namespace Rhubarb\BackgroundTasks\Presenters;

require_once __DIR__ . '/BackgroundTaskPresenter.php';

use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Crown\Context;

class BackgroundTaskFullFocusPresenter extends BackgroundTaskPresenter
{
    private $injectedView;

    private $acceptableWaitTime = false;

    public function __construct(BackgroundTaskFullFocusView $view)
    {
        parent::__construct();

        $this->injectedView = $view;
    }

    protected function createView()
    {
        return $this->injectedView;
    }

    protected function processStartupEvents()
    {
        parent::processStartupEvents();

        $context = new Context();

        if (!$context->getIsAjaxRequest()) {
            if ($this->acceptableWaitTime) {

                usleep($this->acceptableWaitTime);

                $status = new BackgroundTaskStatus($this->model->BackgroundTaskStatusID);
                $status->reload();

                if (!$status->isRunning()) {
                    $this->raiseEvent("TaskComplete", $status->UniqueIdentifier);
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