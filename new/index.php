<?php

/*
    David Gonzalez - CS 3360: Programming Languages
    Last Modified: Oct 10
*/

// This section initializes the game

const STRATEGY = 'strategy';
$strategies = array("smart", "random");

// If given strategy is not specified -> return error message
if (!array_key_exists(STRATEGY, $_GET)) {
    $result = array("response" => false, "reason" => "Strategy not specified");
    echo json_encode($result);
    // If given strategy is valid -> initialize game with a unique game id and defined strategy
} else {
    $strategy = $_GET[STRATEGY];
    $strategy = strtolower($strategy);

    if ($strategy == $strategies[0] || $strategy == $strategies[1]) {
        //Initialize game with chosen strategy
        initializeGame($strategy);
    } else {
        $result = array("response" => false, "reason" => "Unknown Strategy");
        echo json_encode($result);
    }
}
// Function to generate new Game ID if Strategy input is valid
function initializeGame($strategy)
{
    // Generate unique game ID
    $pid = uniqid();
    $result = array("response" => true, "pid" => $pid);
    echo json_encode($result);

    $file_Name = "../data/gameData.txt";
    // CHANGE THIS LATER TO APPEND ---> IN ORDER TO SUPPORT MULTIPLE GAMES ('w'->'a')
    // Another option is to create a different file with name of the generated pid and access game data that way
    $file = fopen($file_Name, "w") or die("Unable to open file!");
    $gameData = array('pid' => $pid, 'strategy' => $strategy, 'player' => [], 'computer' => []);
    // Stores Game data in local file path '../data/gameData.txt'
    fputs($file, json_encode($gameData));
    fclose($file);
}