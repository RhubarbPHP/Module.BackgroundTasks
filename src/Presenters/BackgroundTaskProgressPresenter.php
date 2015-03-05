<?php

namespace Rhubarb\BackgroundTasks\Presenters;

use Rhubarb\Leaf\Presenters\HtmlPresenter;

class BackgroundTaskProgressPresenter extends HtmlPresenter
{
    protected function createView()
    {
        return new BackgroundTaskProgressView();
    }

    public function setBackgroundTaskId( $backgroundTaskId )
    {
        $this->model->BackgroundTaskID = $backgroundTaskId;
    }

    protected function getPublicModelPropertyList()
    {
        $properties = parent::getPublicModelPropertyList();
        $properties[] = "BackgroundTaskID";

        return $properties;
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler( "GetProgress", function() {

        });
    }
}