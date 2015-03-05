<?php

namespace Rhubarb\BackgroundTasks\Presenters;

use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Leaf\Views\HtmlView;
use Rhubarb\Leaf\Views\WithViewBridgeTrait;

class BackgroundTaskProgressView extends HtmlView
{
    use WithViewBridgeTrait;

    protected function printViewContent()
    {
        $backgroundTaskId = $this->GetData("BackgroundTaskID");
        $task = new BackgroundTaskStatus( $backgroundTaskId );

        ?>
        <div class="bar">
            <div class="progress" style="width: <?=$task->PercentageComplete;?>%"></div>
            <p class="message"><?=$task->Message;?></p>
        </div>
        <?php
    }

    /**
     * Implement this and return __DIR__ when your ViewBridge.js is in the same folder as your class
     *
     * @returns string Path to your ViewBridge.js file
     */
    public function getDeploymentPackageDirectory()
    {
        return __DIR__;
    }
}