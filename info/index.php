<?php

/*
    David Gonzalez - CS 3360: Programming Languages
    Last Modified: Oct 10
*/

class GameInfo{
    public $size;
    public $strategies;
    function __construct($size, $strategies) {
        $this->size = $size;
        $this->strategies = $strategies;
    }
}
$strategies = array('Smart', 'Random');
$size = 15;
$info = new GameInfo($size, $strategies);
echo json_encode($info);
