<?php

/*
    David Gonzalez - CS 3360: Programming Languages
    Last Modified: Oct 10
*/

class RandomStrategy{
    function __construct(){
    }
    public function pickCell(Board $board)
    {
        // Check if all cells are full
        $allCellsAreFull = true;
        for ($i = 0; $i < 15; $i++) {
            for ($j = 0; $j < 15; $j++) {
                if ($board->getCell($i, $j) == 0) {
                    $allCellsAreFull = false;
                    break 2;
                }
            }
        }
        // If all cells are full, return null
        if ($allCellsAreFull) {
            return null;
        }
        // If not all cells are full, continue to find an empty one
        while (true) {
            $x = rand(0, 14);
            $y = rand(0, 14);
            if ($board->getCell($x, $y) == 0) {
                $board->setCell(2, $x, $y);
                return [$x, $y];
            }
        }
    }

}

class SmartStrategy
{
    function __construct(){
    }
    public function pickCell(Board $board){
        // Loop from the largest desirable line length to the smallest one.
        for ($lineLength=4; $lineLength >= 2; $lineLength--) {
            // Try to find a move that extends our own line of length $lineLength.
            $move = $this->findBestMove($board, 2, $lineLength);
            if ($move) {
                return $move;
            }

            // Try to find a move that blocks the opponent's line of length $lineLength.
            $move = $this->findBestMove($board, 1, $lineLength);
            if ($move) {
                return $move;
            }
        }
        // If no strategic move is found, select a random cell
        while (true) {
            $x = rand(0, 14);
            $y = rand(0, 14);
            if ($board->getCell($x, $y) == 0) {
                $board->setCell(2, $x, $y);
                return [$x, $y];
            }
        }
    }
    function findBestMove(Board $board, $player, $lineLength){
        // Iterate over all cells in the board
        for ($x = 0; $x < 15; $x++) {
            for ($y = 0; $y < 15; $y++) {
                // Define directions for checking lines: horizontal, vertical, and diagonals
                $directions = [[1, 0], [0, 1], [1, 1], [1, -1]];
                foreach ($directions as $direction) {
                    // Check if there's a line of the specified length in any direction from the current cell
                    if ($this->hasLineOfLength($board, $player, $x, $y, $direction[0], $direction[1], $lineLength)) {
                        // Determine the ends of the line
                        $end1 = [$x - $direction[0], $y - $direction[1]];
                        $end2 = [$x + $direction[0] * $lineLength, $y + $direction[1] * $lineLength];
                        // Check both ends of the line for a possible move
                        foreach ([$end1, $end2] as $end) {
                            if ($this->isValidMove($board, $end[0], $end[1])) {
                                $board->setCell(2, $end[0], $end[1]);
                                return $end;
                            }
                        }
                    }
                }
            }
        }
        // No smart move available
        return null;
    }
    function hasLineOfLength(Board $board, $player, $x, $y, $dx, $dy, $length)
    {
        // Iterate for the specified length and check cells in the direction
        for ($i = 0; $i < $length; $i++) {
            // Ensure that the cell belongs to the specified player
            if (!$this->isValidMove($board, $x + $i * $dx, $y + $i * $dy, $player)) {
                return false;
            }
        }
        return true;
    }
    function isValidMove(Board $board, $x, $y, $player = 0)
    {
        // Check that coordinates are within bounds
        return
            $x >= 0 && $x < 15 &&
            $y >= 0 && $y < 15 &&
            $board->getCell($x, $y) == $player;
    }
}


