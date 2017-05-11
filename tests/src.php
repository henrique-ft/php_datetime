<?php

include 'src/TimeMachine.php';
include 'src/DateMachine.php';
include 'src/Schedule.php';

var_dump(Blacktools\DateTime\TimeMachine::interval('00:00:00','23:59:59'));

$schedule = new Blacktools\DateTime\Schedule();

var_dump($schedule->debug());