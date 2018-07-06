<?php

namespace Rhubarb\Scaffolds\BackgroundTasks\Leaves;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\LeafModel;

class BackgroundTaskModel extends LeafModel
{
	public $backgroundTaskStatusId;

	/**
	 * @var Event Raised when the view needs to know the task progress
	 */
	public $triggerTaskEvent;

	public function __construct()
	{
		parent::__construct();

		$this->triggerTaskEvent = new Event();
	}

	protected function getExposableModelProperties()
	{
		$properties = parent::getExposableModelProperties();
		$properties[] = "backgroundTaskStatusId";
		return $properties;
	}
}