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

namespace Rhubarb\Scaffolds\BackgroundTasks\Models;

use Exception;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Scaffolds\BackgroundTasks\Task;
use Rhubarb\Scaffolds\BackgroundTasks\TaskStatus;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MySqlEnumColumn;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\DateTimeColumn;
use Rhubarb\Stem\Schema\Columns\DecimalColumn;
use Rhubarb\Stem\Schema\Columns\IntegerColumn;
use Rhubarb\Stem\Schema\Columns\JsonColumn;
use Rhubarb\Stem\Schema\Columns\LongStringColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 * Allows for execution of background tasks and persistence of progress data in a model.
 *
 * @property string $TaskClass
 * @property float $PercentageComplete
 * @property string $Message
 * @property string $TaskStatus The status of the task: Running, Complete or Failed
 * @property string $ExceptionDetails If the task failed, exception details will be contained here.
 * @property \stdClass $TaskSettings Settings to be passed to the task, stored as JSON
 * @property int $ProcessID The PID for the background task process when it is executed
 */
class BackgroundTaskStatus extends Model
{
    const TASK_STATUS_RUNNING = "Running";
    const TASK_STATUS_COMPLETE = "Complete";
    const TASK_STATUS_FAILED = "Failed";

    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new ModelSchema("tblBackgroundTaskStatus");
        $schema->addColumn(
            new AutoIncrementColumn("BackgroundTaskStatusID"),
            new StringColumn("TaskClass", 300),
            new MySqlEnumColumn(
                "TaskStatus",
                self::TASK_STATUS_RUNNING,
                [self::TASK_STATUS_COMPLETE, self::TASK_STATUS_FAILED, self::TASK_STATUS_RUNNING]
            ),
            new DateTimeColumn("StartedDate"),
            new DateTimeColumn("LastProgressDate"),
            new DecimalColumn("PercentageComplete", 5, 2, 0),
            new StringColumn("Message", 200),
            new LongStringColumn("ExceptionDetails"),
            new JsonColumn("TaskSettings", null, true),
            new IntegerColumn("ProcessID")
        );

        return $schema;
    }

    protected function beforeSave()
    {
        parent::beforeSave();

        if ($this->isNewRecord()){
            $this->StartedDate = "now";
        }

        $this->LastProgressDate = "now";
    }

    public function isRunning()
    {
        $this->reload();

        return $this->TaskStatus == "Running";
    }
}
