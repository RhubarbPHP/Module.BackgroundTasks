<?php

namespace Rhubarb\BackgroundTasks\Presenters;

use Rhubarb\BackgroundTasks\Models\BackgroundTaskStatus;
use Rhubarb\Leaf\Presenters\HtmlPresenter;

class BackgroundTaskProgressPresenter extends HtmlPresenter
{
    protected function createView()
    {
        return new BackgroundTaskProgressView();
    }

    public function setBackgroundTaskStatusId( $backgroundTaskStatusId )
    {
        $this->model->BackgroundTaskStatusID = $backgroundTaskStatusId;
    }

    protected function getPublicModelPropertyList()
    {
        $properties = parent::getPublicModelPropertyList();
        $properties[] = "BackgroundTaskStatusID";

        return $properties;
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler( "GetProgress", function() {
            $status = new BackgroundTaskStatus( $this->model->BackgroundTaskStatusID );

            $progress = new \stdClass();
            $progress->percentageComplete = $status->PercentageComplete;
            $progress->message = $status->Message;

            return $progress;
        });
    }
}