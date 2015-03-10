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

use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Leaf\Views\HtmlView;

class BackgroundTaskProgressView extends HtmlView
{
    protected function printViewContent()
    {
        $backgroundTaskId = $this->GetData("BackgroundTaskID");
        $task = new BackgroundTaskStatus($backgroundTaskId);

        ?>
        <div class="bar">
            <div class="progress" style="width: <?= $task->PercentageComplete; ?>%"></div>
            <p class="message"><?= $task->Message; ?></p>
        </div>
        <?php
    }

    public function getDeploymentPackage()
    {
        $package = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . '/BackgroundTaskViewBridge.js';
        $package->resourcesToDeploy[] = __DIR__ . '/BackgroundTaskProgressViewBridge.js';

        return $package;
    }

    protected function getClientSideViewBridgeName()
    {
        return "BackgroundTaskProgressViewBridge";
    }
}