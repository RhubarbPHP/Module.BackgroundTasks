<?php

use Rhubarb\Scaffolds\BackgroundTasks\Task;
use Rhubarb\Scaffolds\BackgroundTasks\TaskStatus;

class SlowTask extends Task
{
    public $iterations;

    /**
     * Executes the long running code.
     *
     * @param callable $statusCallback A callback providing the task with a means to report progress.
     *                                 A TaskStatus object should be passed.
     * @return void
     */
    public function execute(callable $statusCallback)
    {
        for($this->iterations = 0; $this->iterations < 100; $this->iterations++){
            usleep(100000);

            $statusCallback(new TaskStatus($this->iterations, "Busy bee..."));
        }
    }
}