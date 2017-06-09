<?php

include '../src/ErrorHandler.php';
include '../src/TimeMachine.php';
include '../src/DateMachine.php';
include '../src/Schedule.php';

$schedule = new Blacktools\DateTime\Schedule([
        
			'table_attributes' => '',
			'thead_attributes' => '',
			'tbody_attributes' => '',
			'caption_content' => '',
  			'caption_attributes' => '',
			'hours_attributes' => '',
			'week_days_attributes' => '',
			'month_days_attributes' => '',
			'show_date_format' => 'Y-m-d',
			'show_time_format' => 'H:i:s',
			'show_hour_interval' => false,
			'show_week_days' => true,
			'language' => 'PT',
			'work_by' => 'calendar',
			'hour_interval' => '01:00:00' 
        ]);
        
echo '<pre>';

var_dump($schedule->cells);
