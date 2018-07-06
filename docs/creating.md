Creating a Task
===============

Create a task class by extending Task. The `execute` function is abstract and it is here
where your task processing takes place.

~~~php
class MyTask extends Task
{
    public function execute(callable $statusCallback)
    {
        // Doing something really slow here...
        for($x = 0; $x < 100; $x++){
            sleep(1);
        }
    }
}
~~~

If you want to report your progress you should call the `$statusCallback` callback and
pass a TaskStatus object giving a percentage complete and a message.

~~~php
class MyTask extends Task
{
    public function execute(callable $statusCallback)
    {
        $length = 100;
        
        // Doing something really slow here...       
        for($x = 0; $x < $length; $x++){
        
            $statusCallback(
                new TaskStatus(
                    $x * 100 / $length,
                    "Sleeping quietly..."
                    )
                );
                
            sleep(1);
        }
    }
}
~~~