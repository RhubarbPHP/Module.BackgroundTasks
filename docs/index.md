Background Tasks [packagist:rhubarbphp/scaffold-background-tasks]
================

When you need to execute long running tasks triggered by a user action but disconnected
from the originating request you can use the background tasks scaffold.

This is achieved using one of two techniques:

1. By invoking the task using a CLI wrapper that disconnects completely from any
originating caller using the [pnctl](http://php.net/manual/en/intro.pcntl.php) PHP library

2. By invoking the task from a leaf UI using a special server event and JSON streaming.

Aside from "backgrounding" support this module provides a nice pattern for any data processing
task through the `Task` class and it's callback approach to reporting progress.

> Note that the CLI method does not work on Windows - only on OSes supporting the
> [pnctl](http://php.net/manual/en/intro.pcntl.php) PHP library.
