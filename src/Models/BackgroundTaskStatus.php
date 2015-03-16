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

require_once __DIR__ . '/../../../module-stem/src/Models/Model.php';

use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Decimal;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Enum;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\MediumText;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlSchema;

/**
 * Allows for execution of background tasks and persistence of progress data in a model.
 *
 * @property string $TaskClass
 * @property float $PercentageComplete
 * @property string $Message
 * @property string $TaskStatus The status of the task: Running, Complete or Failed
 * @property string $ExceptionDetails If the task failed, exception details will be contained here.
 */
class BackgroundTaskStatus extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new MySqlSchema("tblBackgroundTaskStatus");
        $schema->addColumn(
            new AutoIncrement("BackgroundTaskStatusID"),
            new Varchar( "TaskClass", 300 ),
            new Enum("TaskStatus", "Running", [ "Running", "Complete", "Failed" ] ),
            new Decimal("PercentageComplete", "5,2", 0),
            new Varchar("Message",200),
            new MediumText("ExceptionDetails")
        );

        return $schema;
    }

    /**
     * Starts the background task by instantiating the task class and calling execute.
     */
    public function start()
    {
        $class = $this->TaskClass;
        $task = new $class();

        try {
            $task->execute($this);
            $this->TaskStatus = "Complete";
        } catch ( RhubarbException $er ) {
            $this->TaskStatus = "Failed";
            $this->ExceptionDetails = $er->getMessage()."\r\n\r\n".$er->getTraceAsString();
        }

        $this->save();
    }

    public function isRunning()
    {
        $this->reload();

        return $this->TaskStatus == "Running";
    }
}