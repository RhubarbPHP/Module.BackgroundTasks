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
     * @var callable
     */
    private $taskMaker;

    /**
     * @var Event Called to get the finale response object for client side digestion
     */
    public $getResultEvent;

    public function __construct(callable $taskMaker, $name = "")
    {
        parent::__construct($name);

        $this->taskMaker = $taskMaker;
        $this->getResultEvent = new Event();
    }

    public function setBackgroundTaskStatusId($backgroundTaskStatusId)
    {
        $this->model->backgroundTaskStatusId = $backgroundTaskStatusId;
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->triggerTaskEvent->attachHandler(function(...$arguments){

            $taskMaker = $this->taskMaker;
            $task = $taskMaker(...$arguments);

            ignore_user_abort(true);

            while(ob_get_level()>0) {
                ob_end_clean();
            }

            $lastStatus = null;
            $result = \Rhubarb\Scaffolds\BackgroundTasks\BackgroundTask::executeInBackground($task,function ($status) use (&$lastStatus){

                $status->percentageComplete = round($status->percentageComplete,0);

                if ((!$lastStatus || ($status->percentageComplete != $lastStatus->percentageComplete))
                    && ($status->status == BackgroundTaskStatus::TASK_STATUS_RUNNING)) {
                    print json_encode($status) . "\r\n";

                    flush();
                }

                $lastStatus = $status;
            });

            if ($lastStatus->status == BackgroundTaskStatus::TASK_STATUS_COMPLETE ||
                $lastStatus->status == BackgroundTaskStatus::TASK_STATUS_FAILED ){
                $lastStatus->result = $this->getResult($result);
            }

            print json_encode($lastStatus) . "\r\n";

            flush();

            exit;
        });
    }

    /**
     * Override to return a data structure that is passed to the client side onComplete method.
     * @return \stdClass
     */
    protected function getResult($resultFromTask)
    {
        $response = $this->getResultEvent->raise($resultFromTask);

        return $response ?? $resultFromTask;
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
