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

use Rhubarb\Crown\Events\Event;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Leaf\Leaves\HtmlPresenter;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Scaffolds\BackgroundTasks\TaskStatus;

class BackgroundTask extends Leaf
{
    /**
     * @var BackgroundTaskModel
     */
    protected $model;
    /**
     * @var \Rhubarb\Scaffolds\BackgroundTasks\Task
     */
    private $task;

    /**
     * @var Event Called to get the finale response object for client side digestion
     */
    public $getResultEvent;

    public function __construct(\Rhubarb\Scaffolds\BackgroundTasks\Task $task, $name = "")
    {
        parent::__construct($name);

        $this->task = $task;
        $this->getResultEvent = new Event();
    }

    public function setBackgroundTaskStatusId($backgroundTaskStatusId)
    {
        $this->model->backgroundTaskStatusId = $backgroundTaskStatusId;
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->triggerTaskEvent->attachHandler(function(){
            while(ob_get_level()>0) {
                ob_end_clean();
            }

            \Rhubarb\Scaffolds\BackgroundTasks\BackgroundTask::executeInBackground($this->task,function ($status) {

                if ($status->status == BackgroundTaskStatus::TASK_STATUS_COMPLETE ||
                    $status->status == BackgroundTaskStatus::TASK_STATUS_FAILED ){
                    $status->result = $this->getResult();
                }

                print json_encode($status) . "\r\n";

                flush();
            });

            exit;
        });
    }

    /**
     * Override to return a data structure that is passed to the client side onComplete method.
     * @return \stdClass
     */
    protected function getResult()
    {
        return $this->getResultEvent->raise();
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        return new BackgroundTaskModel();
    }

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return BackgroundTaskView::class;
    }
}
