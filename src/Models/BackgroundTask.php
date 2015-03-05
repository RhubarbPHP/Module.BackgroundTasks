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
 * Thanks to Tony in the comments of http://php.net/manual/en/function.pcntl-fork.php#94338 for the tips
 * on running as a daemon using pcntl functions.
 */
class BackgroundTask extends Model
{
    /**
     * Returns the schema for this data object.
     *
     * @return \Rhubarb\Stem\Schema\ModelSchema
     */
    protected function createSchema()
    {
        $schema = new MySqlSchema("tblBackgroundTask");
        $schema->addColumn(
            new AutoIncrement("BackgroundTaskID"),
            new Decimal("PercentageComplete", 5, 2),
            new Varchar("Message",200)
        );

        return $schema;
    }

    public function __get($propertyName)
    {
        // Accessing the task object is normally used to poll for new state information. We make sure here that
        // for all properties except the unique identifier we reload the model.
        if (!$this->isNewRecord() && $propertyName != "UniqueIdentifier" && $propertyName != $this->uniqueIdentifierColumnName) {
            $this->reload();
        }

        return parent::__get($propertyName);
    }

    /**
     * Executes the call back in the background and returns a BackgroundTask object
     *
     * @param $callBack
     * @return BackgroundTask|null
     * @throws \Exception
     */
    public static function execute($callBack)
    {
        // Create an entry in our database.
        $task = new BackgroundTask();
        $task->save();

        $shutdown = function() {
            posix_kill(posix_getpid(), SIGHUP);
        };

        $pid = pcntl_fork();

        if( $pid ){
            // The parent can go on with it's business
            return $task;
        }

        ob_end_clean(); // Discard the output buffer and close

        fclose(STDIN);  // Close all of the standard
        fclose(STDOUT); // file descriptors as we
        fclose(STDERR); // are going to run as a daemon.

        register_shutdown_function($shutdown);

        if (posix_setsid() < 0) {
            exit;
        }

        if ($pid = pcntl_fork()) {
            exit;
        }

        // Now running as a daemon. This process will even survive
        // an apachectl stop

        $callBack($task);

        exit;
    }
}