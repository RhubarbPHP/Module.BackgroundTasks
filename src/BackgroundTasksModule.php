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

namespace Rhubarb\Scaffolds\BackgroundTasks;

use Rhubarb\Crown\Module;
use Rhubarb\Stem\Schema\SolutionSchema;

class BackgroundTasksModule extends Module
{
    public static $debugServerName = "backgroundtask";

    protected function initialise()
    {
        parent::initialise();

        SolutionSchema::registerSchema("BackgroundTasks", __NAMESPACE__ . '\Models\BackgroundTasksSolutionSchema');
    }
}
