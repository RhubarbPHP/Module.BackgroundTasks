<?php

namespace Rhubarb\BackgroundTasks\Scripts;

use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;

$taskId = intval( $argv[2] );

if ( !$taskId )
{
    die( "No background task specified" );
}

$pid = pcntl_fork();

if( $pid ){
    print "Spawned $pid";
    exit;
}

if (posix_setsid() < 0) {
    exit;
}

$task = new BackgroundTaskStatus( $taskId );
$task->start();