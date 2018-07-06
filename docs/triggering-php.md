Triggering from PHP
===================

The cli wrapper can be used to invoke the task in a completely separate instance of PHP running
using the cli. The process is forked so that it continues even when the invoking parent task
ends.

To invoke the task call the static `initiate()` function on your class.

> Note that the `initiate()` function is static because the object itself does not pass
> to the background PHP process - only the class name. Any instance properties would be therefore be lost.
> See below for details on how to overcome this limitation by passing a serialisable array of settings.

~~~php
$status = MyTask::initiate();
~~~

A BackgroundTaskStatus object is shared between the execute function and the caller to initiate so that
the progress of the task can be monitored.

~~~php

while ($status->isRunning()){
    print "Still running....";
    sleep(5);
}

// Should output "Still running" 5 times.
~~~

The BackgroundTaskStatus object defines a number of useful functions and properties:

BackgroundTaskStatus::$BackgroundTaskStatusID
:   The unique ID for this background task.
BackgroundTaskStatus::$PercentageComplete
:   Returns the completion status as a percentage
BackgroundTaskStatus::$Message
:   Returns the message about what the task is currently doing

## Passing arguments to the task runner

In some circumstances you need to pass arguments to the task running on the background thread. There are
two types of arguments; standard arguments and shell arguments.

### Standard Arguments

If you pass arguments to the initiate() function, they will be available to your background task in its
execute function by accessing the `TaskSettings` property of the status object:

~~~php
$status = TerminatorTask::initiate(
    [
        "mission" => "destroy",
        "target" => "humans"
    ]);
~~~

~~~php
class TerminatorTask extends BackgroundTask
{
    public function execute( BackgroundTaskStatus $status )
    {
        // Pick up task settings from the array.
        if ( $status->TaskSettings["mission"] == "destroy" ) {
            $target = $status->TaskSettings["target"];
        }
    }
}
~~~

### Shell arguments

Sometimes arguments need to be passed to the task before database access is available. Most often this is
when access to the database is only possible using values only the parent task knew. In this case these values
must be passed to the task runner.

> Passing shell arguments is a big security risk. Those arguments will be visible should an attacker be able to
> get a process list from your server. If you are passing shell arguments you are strongly advised to encrypt them
> or provide some other interim step in calculating the required values.

There is no mechanism for simply passing shell arguments as you can with standard arguments.
Instead you must override the `getShellArguments()` function and
return an array of the arguments to pass. The arguments can be retrieved in the `execute()`
method by accessing the $shellArguments array:

~~~php
class TerminatorTask extends BackgroundTask
{
    protected static function getAdditionalTaskRunnerArguments()
    {
        // Note these values are insecure - they should be encrypted.
        return ["command-hq", "arnie", "letmein123"];
    }

    public function execute(BackgroundTaskStatus $status)
    {
        // Connect to command and control
        $settings = StemSettings::singleton();
        $settings->host = $this->shellArguments[0];
        $settings->username = $this->shellArguments[1];
        $settings->password = $this->shellArguments[2];
    }
}
~~~