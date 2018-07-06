<?php

use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;

class BasicBehaviour extends Leaf
{
    /**
    * @var LeafModel
    */
    protected $model;
    
    protected function getViewClass()
    {
        return BasicBehaviourView::class;
    }
    
    protected function createModel()
    {
        $model = new LeafModel();

        return $model;
    }
}