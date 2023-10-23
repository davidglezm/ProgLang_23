<?php

/*
    David Gonzalez - CS 3360: Programming Languages
    Last Modified: Oct 10
*/

include 'Board.php';
include 'moveStrategy.php';

// Class to perform all required operations to play OMOK.

class Play {

    public $board; //Board with default size of 15
    public $playerWin; //True: player won, default false
    public $playerDraw; //True: player and computer drew, default false
    public $playerRow; //Contains winning row
    public $computerWin; //True: computer won, default false
    public $computerDraw; //True: computer drew person, default false
    public $computerRow; //Contains winning row (x,y) pairs
    public $computerStrategy; //Stores game strategy

    function __construct(){
        // Initialization of play class attributes
        $this->board = new Board();
        $this->playerWin = false;
        $this->playerDraw = false;
        $this->playerRow = [];
        $this->computerWin = false;
        $this->computerDraw = false;
        $this->computerRow = [];
    }

    function game($gameData){
        // Setting computer's strategy
        $this->computerStrategy = $gameData->strategy;

        // Process player moves if they exist in the game data
        if (!empty($gameData->player)) {
            $this->processMoves($gameData->player, 1);
        }

        // Process computer moves if they exist in the game data
        if (!empty($gameData->computer)) {
            $this->processMoves($gameData->computer, 2);
        }
    }
    function processMoves($moves, $player){
        // Loop through each move and set the cell on the board for the player type
        foreach ($moves as $move) {
            $this->board->setCell($player, $move[0], $move[1]);
        }
    }
    function playerMove($playerMove){
        // Extract x and y coordinates from the player move.
        list($x, $y) = $playerMove;

        if ($this->board->getCell($x, $y) != 0) {
            return false;
        }

        // Set the chosen cell for the player and check if the move results in a win
        $this->board->setCell(1, $x, $y);
        $this->isWin(1, $x, $y);

        // If the move did not result in a win, check for a draw
        if (!$this->playerWin) {
            $this->playerDraw = $this->isDraw();
        }
        return true;
    }
    function computerMove($playerMove){
        // Determine the strategy to use for the computer move
        $strategy = ($this->computerStrategy == "random") ? new RandomStrategy() : new SmartStrategy();
        // Determine computer move based on the chosen strategy
        $computerMove = ($this->computerStrategy == "random")
            ? $strategy->pickCell($this->board)
            : $strategy->pickCell($this->board, $playerMove);
        // Check whether the computer moves result in a win or a draw
        $this->processComputerMove($computerMove);
        return $computerMove;
    }
    function processComputerMove($computerMove){
        // Extract x and y coordinates from computer move
        list($x, $y) = $computerMove;

        // Check if the move results in a win for the computer
        $this->isWin(2, $x, $y);

        // If the move did not result in a win, check for a draw
        if (!$this->computerWin) {
            $this->computerDraw = $this->isDraw();
        }
    }

    // Performs vertical, horizontal and diagonal checks
    function isWin($user, $x, $y){
        $x = (int)$x;
        $y = (int)$y;
        if(!$this->checkHorizontal($user, $x, $y)) {
            if(!$this->checkVertical($user, $x, $y)) {
                //Still need to implement the diagonal checks
                return;
            }
            return;
        }
    }
    // Checks for non-empty board
    function isDraw() {
        for ($i = 0; $i < 15; $i++) {
            for ($j = 0; $j < 15; $j++) {
                if ($this->board->getCell($i, $j) == 0) {
                    // Game ongoing, not a draw
                    return false;
                }
            }
        }
        // All cells filled, it's a draw
        return true;
    }
    function findNull($row) {
        return in_array(null, $row, true);
    }
    function samePlayerType($row, $user) {
        for ($i = 0; $i < 5; $i++) {
            if($row[$i] != $user) {
                return false;
            }
        }
        return true;
    }

    // Multi-directional checks for 5 consecutive stones
    function checkVertical($user, $x, $y){
        // Define the shifts for vertical checks
        $shifts = [
            [0, 1, 2, 3, 4],
            [-1, 0, 1, 2, 3],
            [-2, -1, 0, 1, 2],
            [-3, -2, -1, 0, 1],
            [-4, -3, -2, -1, 0]
        ];

        // Iterate through each shift pattern
        foreach ($shifts as $shift) {
            $row = array_map(function ($i) use ($x, $y) {
                return $this->board->getCell($x, $y + $i);
            }, $shift);

            // If no null found and all belong to the user, declare a win
            if (!$this->findNull($row) && $this->samePlayerType($row, $user)) {
                // Update winner and winning row status based on user
                $prefix = $user === 1 ? 'player' : 'computer';
                $this->{$prefix . 'Win'} = true;
                $this->{$prefix . 'Row'} = array_merge(...array_map(function ($i) use ($x, $y) {
                    return [$x, $y + $i];
                }, $shift));
                return true;
            }
        }
        return false;
    }
    function checkHorizontal($user, $x, $y){
        // Define the shifts for horizontal checks
        $shifts = [
            [0, 1, 2, 3, 4],
            [-1, 0, 1, 2, 3],
            [-2, -1, 0, 1, 2],
            [-3, -2, -1, 0, 1],
            [-4, -3, -2, -1, 0]
        ];

        // Iterate through each shift pattern
        foreach ($shifts as $shift) {
            $row = array_map(function ($i) use ($x, $y) {
                return $this->board->getCell($x + $i, $y);
            }, $shift);

            // If no null found and all belong to the user, declare a win
            if (!$this->findNull($row) && $this->samePlayerType($row, $user)) {
                // Update winner and winning row status based on user
                $prefix = $user === 1 ? 'player' : 'computer';
                $this->{$prefix . 'Win'} = true;
                $this->{$prefix . 'Row'} = array_merge(...array_map(function ($i) use ($x, $y) {
                    return [$x + $i, $y];
                }, $shift));
                return true;
            }
        }
        return false;
    }
    // FINISH
    function checkLeftDiagonal($user, $x, $y){
        return;
    }
    function checkRightDiagonal($user, $x, $y){
        return;
    }

    // Output to be displayed for user
    function displayOutput($user, $move)
    {
        $x = (int)$move[0];
        $y = (int)$move[1];

        // Determine player type(player, computer)
        $isPlayer = $user == 1;
        $isWin = $isPlayer ? $this->playerWin : $this->computerWin;
        $isDraw = $isPlayer ? $this->playerDraw : $this->computerDraw;
        $row = $isPlayer ? $this->playerRow : $this->computerRow;

        // Create the base result array
        $result = array(
            'x' => $x,
            'y' => $y,
            'isWin' => $isWin,
            'isDraw' => $isDraw,
            'row' => []
        );

        // Modify 'row' only if there's a win
        if ($isWin) {
            $result['row'] = array_slice($row, 0, 10);  // Assuming you want the first 10 elements.
        }

        return $result;
    }
}
