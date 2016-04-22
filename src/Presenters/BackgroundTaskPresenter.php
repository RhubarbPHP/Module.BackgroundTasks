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

namespace Rhubarb\Scaffolds\BackgroundTasks\Presenters;

use Rhubarb\Leaf\Presenters\HtmlPresenter;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;

class BackgroundTaskPresenter extends HtmlPresenter
{
    public function setBackgroundTaskStatusId($backgroundTaskStatusId)
    {
        $this->model->BackgroundTaskStatusID = $backgroundTaskStatusId;
    }

    protected function getPublicModelPropertyList()
    {
        $properties = parent::getPublicModelPropertyList();
        $properties[] = "BackgroundTaskStatusID";

        return $properties;
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler("GetProgress", function () {
            $status = new BackgroundTaskStatus($this->model->BackgroundTaskStatusID);

            $progress = new \stdClass();
            $progress->percentageComplete = $status->PercentageComplete;
            $progress->message = $status->Message;
            $progress->isRunning = $status->isRunning();
            $progress->taskStatus = $status->TaskStatus;

            return $progress;
        });
    }
}
