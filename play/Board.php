<?php

/*
    David Gonzalez - CS 3360: Programming Languages
    Last Modified: Oct 10
*/

class Board {
    public $size;
    public $board;
    public function __construct(){
        //Board size of 15x15 by default
        $this->size = 15;
        //Initialize Board with all 0's -> empty
        $this->initializeBoard();
    }
    private function initializeBoard(){
        //Initialize array with all 0's
        $this->board = array_fill(0, $this->size, array_fill(0, $this->size, 0));
    }
    public function getCell($x, $y)
    {
        if($x > $this->size - 1 || $x < 0 || $y > $this->size - 1 || $y < 0){
            return null;
        }
        return $this->board[$x][$y];
    }
    public function checkEmpty($x, $y){
        if ($this->board[$x][$y] == 0){
            return true;
        }
        return false;
    }
    public function setCell($value, $x, $y){
        if ($x >= 0 && $x < $this->size && $y >= 0 && $y < $this->size && $this->getCell($x, $y) == 0) {
            $this->board[$x][$y] = $value;
        }
    }
    # No longer in use ---
    public function findNextEmptyCell($x, $y){
        for($y_iter = $y; $y_iter < $this->size; $y_iter++){
            for($x_iter = 0; $x_iter < $this->size; $x_iter++){
                if($this->board[$x_iter][$y_iter] == 0){
                    return [$x_iter, $y_iter];
                }
            }
        }
        return [$x, $y];
    }
    public function display() {
        for ($i = 0; $i < $this->size; $i++) {
            for ($j = 0; $j < $this->size; $j++) {
                echo $this->board[$i][$j] . " ";
            }
            echo PHP_EOL;
        }
        echo "- - - - - - - - - - - - - - -";
        echo "
";
    }
}