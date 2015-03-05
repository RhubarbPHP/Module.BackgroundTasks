<?php

namespace Rhubarb\BackgroundTasks\Tests\Models;

use Rhubarb\BackgroundTasks\Models\BackgroundTask;
use Rhubarb\BackgroundTasks\Models\BackgroundTasksSolutionSchema;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Stem\Tests\Repositories\MySql\MySqlTestCase;

class BackgroundTaskTest extends MySqlTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Make sure the unit testing database has our table - we'll need it later.
        $schema = new BackgroundTasksSolutionSchema();
        $schema->checkModelSchemas();
    }

    public function testBackgroundTaskRuns()
    {
        $sh = shmop_open(1, "c", 0644, 1 );
        shmop_write( $sh, "0", 0 );

        BackgroundTask::execute(function() use ($sh)
        {
            shmop_write( $sh, "1", 0 );
        });

        usleep( 1000 );

        $this->assertEquals( 1, shmop_read($sh,0,1), 'The task should have set $a to 1' );
    }

    public function testBackgroundTaskRunsInBackground()
    {
        $sh = shmop_open(1, "c", 0644, 1 );
        shmop_write( $sh, "0", 0 );

        $task = BackgroundTask::execute(function($bt) use ($sh)
        {
            shmop_write( $sh, "1", 0 );
            $bt->Message = "Foo";
            $bt->save();
            usleep( 20000 );
            $bt->Message = "Bar";
            $bt->save();
        });

        usleep( 100 );

        $this->assertEquals( 0, shmop_read($sh,0,1), 'The task shouldn\'t have completed yet so $a should still be 0' );

        $lastTask = BackgroundTask::findLast();

        $this->assertEquals( $lastTask->UniqueIdentifier, $task->UniqueIdentifier, "The execute method should have".
            "returned the ID from tblBackgroundTask" );

        $this->assertEquals("Foo", $lastTask->Message, "The task should be able to set the message");

        usleep( 20000 );

        $this->assertEquals("Bar", $lastTask->Message, "The task should be able to set the message");
    }
}
