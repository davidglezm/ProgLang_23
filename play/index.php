<?php

/*
    David Gonzalez - CS 3360: Programming Languages
    Last Modified: Oct 10
*/

include 'play.php';

// Constants for accessing _GET variables
const PID = 'pid';
const MOVE = 'move';

function outputResponse($response, $reason = '')
{
    echo json_encode(['response' => $response, 'reason' => $reason]);
    exit;
}

// Check if PID and MOVE are specified, if not, output appropriate failure response
if (!isset($_GET[PID], $_GET[MOVE])) {
    outputResponse(false, isset($_GET[PID]) ? "Move not specified" : "PID not specified");
}

// Retrieve and decode game data file
$file = file_get_contents("../data/gameData.txt");
$read_File = json_decode($file);

// Verify PID
if ($_GET[PID] != $read_File->pid) {
    outputResponse(false, "Unknown pid");
}

// Validate MOVE format
$move = explode(",", $_GET[MOVE]);
if (count($move) !== 2 || !is_numeric($move[0]) || !is_numeric($move[1])) {
    outputResponse(false, "Move not well-formed");
}

// Validate the MOVE x coordinate
if ($move[0] < 0 || $move[0] > 14) {
    outputResponse(false, "Invalid x coordinate, $move[0]");
}

// Validate the MOVE y coordinate
if ($move[1] < 0 || $move[1] > 14) {
    outputResponse(false, "Invalid y coordinate, $move[1]");
}

// Initiate a game
$game = new Play();
$game->game($read_File);

// Check if the player's move is valid. If not, give error message
if (!$game->playerMove($move)) {
    outputResponse(false, "Place not empty, ($move[0], $move[1])");
}

// If player wins or draw occurs after player's move, give results response
if ($game->playerWin || $game->playerDraw) {
    outputResponse(true, $game->displayOutput(1, $move));
}

// Computer makes a move
$computer_Move = $game->computerMove($move);

// If computer wins or draw occurs after computer's move, give results response
if ($game->computerWin || $game->computerDraw) {
    outputResponse(true, $game->displayOutput(2, $move));
}

// Output response for both player and computer moves
$result = [
    'response' => true,
    'ack_move' => $game->displayOutput(1, $move),
    'move' => $game->displayOutput(2, $computer_Move)
];
echo json_encode($result);

// Update game data file with new moves
$read_File->player[] = $move;
$read_File->computer[] = $computer_Move;
$file_Name = "../data/gameData.txt";
file_put_contents($file_Name, json_encode($read_File));
