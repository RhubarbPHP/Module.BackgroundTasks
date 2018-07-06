<?php

use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;
use Rhubarb\Leaf\Views\View;
use Rhubarb\Scaffolds\BackgroundTasks\Leaves\BackgroundTask;

class BasicBehaviourView extends View
{
    /**
    * @var BasicBehaviourModel
    */
    protected $model;

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $task = new SlowTask();

        $this->registerSubLeaf(
            $taskRunner = new BackgroundTask($task, "task-runner")
        );

        $taskRunner->getResultEvent->attachHandler(function() use ($task){
            return "Task completed in ".$task->iterations." iterations.";
        });

    }

    public function getDeploymentPackage()
    {
        return new LeafDeploymentPackage(__DIR__.'/BasicBehaviourViewBridge.js');
    }

    protected function getViewBridgeName()
    {
        return "BasicBehaviourViewBridge";
    }

    protected function printViewContent()
    {
        print "<a href='#'>Start</a>";
        print "<p class='progress'></p>";
        print $this->leaves["task-runner"];
    }
}