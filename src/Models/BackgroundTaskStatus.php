<?php

namespace Rhubarb\BackgroundTasks\Models;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\AutoIncrement;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Decimal;
use Rhubarb\Stem\Repositories\MySql\Schema\Columns\Varchar;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlSchema;

/**
 * Allows for execution of background tasks and persistence of progress data in a model.
 *
 * @property string $TaskClass
 * @property float $PercentageComplete
 * @property string $Message
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
            new Decimal("PercentageComplete", "5,2", 0),
            new Varchar("Message",200)
        );

        return $schema;
    }

    public function start()
    {
        $class = $this->TaskClass;
        $task = new $class();

        $task->execute($this);
    }
}