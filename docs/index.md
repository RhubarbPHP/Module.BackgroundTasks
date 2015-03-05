Background Tasks
================

When you need to execute long running tasks triggered by a user action you can use the background tasks
scaffold.

Note that this module does not work on Windows - only on OSes supporting the
[pnctl](http://php.net/manual/en/intro.pcntl.php) PHP library.

## Triggering a background task

Simply call the static BackgroundTask::executeTask() function and pass a call back to the code you want
ran in the background.

~~~php
BackgroundTask::executeTask( function()
{

});
~~~

BackgroundTask::executeTask starts executing the background task immediately on a different thread and
returns a BackgroundTask object which can be used to interrogate the status of the background task

~~~php
$task = BackgroundTask::execute( function()
{
    sleep(16);
});

while ($task->isRunning()){
    print "Still running....";
    sleep(5);
}

// Should output "Still running" 5 times.
~~~

The BackgroundTask object defines a number of useful functions and properties:

BackgroundTask::$backgroundTaskId
:   The unique ID for this background task.
BackgroundTask::fromTaskId($backgroundTaskId)
:   Static method to creates a background task from a given existing task ID
BackgroundTask::percentageComplete()
:   Returns the completion status as a percentage
BackgroundTask::message()
:   Returns a message about what the task is currently doing
BackgroundTask::kill()
:   Instructs the task to end

## Progress Bar support

This module includes a presenter you can use to act as a progress bar for the background task. Here is a sample
Presenter and View where a background task is executed on the event of a button being pushed that then displays
a progress bar.

~~~ php
class DemoPresenter extends FormPresenter
{
    private $longTask;

    protected function createView()
    {
        return new DemoView();
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler("StartLongTask", function()
        {
            // Run the long task but capture the returned task object.
            $this->longTask = BackgroundTask::execute(function()
            {
                // Do something hard.
                reticulateSplines();
            });
        });
    }

    protected function applyModelToView()
    {
        // Pass the
        $this->view->longTaskId = $this->longTask->backgroundTaskId;
    }
}

class DemoView extends HtmlView
{
    public $longTaskId = null;

    protected function createPresenters()
    {
        $this->addPresenters(
            new Button( "DoLongTask", "Reticulate The Splines", function()
            {
                $this->raiseEvent( "StartLongTask" );
            }),
            new BackgroundProgressPresenter( "Progress" )
        );
    }

    protected function configurePresenters()
    {
        if ( $this->longTaskId ) {
            // If we have a background task ID we need to pass this to our progress presenter
            $this->presenters[ "Progress" ]->setBackgroundTaskId = $this->longTaskId;
        }
    }

    public function printViewContent()
    {
        print $this->presenters[ "DoLongTask" ];

        if ( $this->longTaskId ) {
            // Only if we have a background task should we print the progress presenter.
            print $this->presenters[ "Progress" ];
        }
    }
}
~~~