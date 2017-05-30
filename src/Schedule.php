<?php

/**
*
* @author Henrique Fernandez Teixeira
*
* Here are all you need to create and display a schedule object in your web application
*     
*/

namespace Blacktools\DateTime;

use Blacktools\DateTime\TimeMachine;
use Blacktools\DateTime\DateMachine;

use Blacktools\DateTime\ErrorHandler;

class Schedule
{

	private $date;
	private $time;
	private $dates;
	private $week_days;
	private $week_days_numeric;
	private $hours;
	private $cells;
	private $config;

    /**
     * @param array $settings
     */
	public function __construct($settings = [])
	{
		try {

            if (!is_array($settings)) {
                    
                throw new \Exception("Config parameter must be a array");
            }
            
            $errors = [];

			$this->date = new DateMachine();
			$this->time = new TimeMachine();

			$this->config = [

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
					'show_hour_interval' => true,
					'start_date' => date("Y-m-d"),
					'end_date' => DateMachine::addDays(date("Y-m-d"), 7, false),
					'show_week_days' => true,
					'language' => 'PT',
					'start_hour' => '00:00:00',
					'end_hour' => '23:59:59',
					'work_by' => 'all',
					'hour_interval' => '01:00:00',

				];

			$this->config($settings);

		} catch(\Exception $e) {

	        ErrorHandler::displayErrorAndDie($e);
		}
	}

    /**
     * @param string $property
     */
	public function __get($property) {

		if (property_exists($this, $property)) {

			return $this->$property;
		}
	}

    /**
     * @param array $settings
     */
	public function config($settings)
	{
		
		try {
		
			if (!is_array($settings)) {
	                    
	            throw new \Exception("Config parameter must be a array");
	        }
	
			$this->config = array_replace( $this->config, array_intersect_key( $settings, $this->config ) );
			$this->date->config( ['show_format' => $this->config['show_date_format'] ] );
			$this->time->config( ['show_format' => $this->config['show_time_format'] ] );
	
			if ($this->config['work_by'] === 'week_day') {
	
				$this->week_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
				$this->week_days_numeric = [0,1,2,3,4,5,6];
	
			} else {
	
				$this->dates = $this->date->interval( $this->config['start_date'], $this->config['end_date'] );
				$this->week_days = $this->date->weekDays( $this->config['start_date'], $this->config['end_date'], 'name');
				$this->week_days_numeric = $this->date->weekDays( $this->config['start_date'], $this->config['end_date']);
			}
			
			if ($this->config['start_hour'] === '00:00:00' && $this->config['end_hour'] === '00:00:00') {
				
				$this->config['end_hour'] = '23:59:59';
			}
			
			$this->hours = $this->time->interval( $this->config['start_hour'], $this->config['end_hour'], $this->config['hour_interval'] );
	
			$this->cellsGenerate();
			
        } catch(\Exception $e) {
            
            ErrorHandler::displayError($e);
        }

	}

    /**
     * @return array|null
     */
	private function cellsGenerate()
	{

		try {

			$cells = [];

			if ($this->config['work_by'] === 'week_day') {

				foreach ( $this->week_days_numeric as $day ) {
				
					foreach ( $this->hours as $hour ) {
						
						$cells[$day][$hour] = array( 'content' => '', 'attributes' => '');
					}

				}


			} elseif ($this->config['work_by'] === 'month_day') {

				foreach ( $this->dates as $date ) {
				
					foreach ( $this->hours as $hour ) {
						
						$cells[$date][$hour] = array( 'content' => '', 'attributes' => '');
					}

				}

			} else { 

				foreach (  $this->dates as $date ) {
				
					foreach ( $this->week_days_numeric as $day ) {
						
						foreach ( $this->hours as $hour ) {
							
							$cells[$date][$day][$hour] = array( 'content' => '', 'attributes' => '');
						}
					}

				}

			}

			$this->cells = $cells;

		} catch(\Exception $e) {

			ErrorHandler::displayError($e);
            
            return null;
		}

	}

    /**
     * @return strings
     */
	public function debug()
	{

			$debug = '<table>';

				if ($this->config['work_by'] !== 'week_day') {

					$debug .= '<tr> <th></th>';

						foreach ( $this->dates as $date ) {

							$debug .= '<th>' . $this->date->toShow($date) . '</th>';

						}

					$debug .= '</tr>';	
				}

			$debug .= '<tr> <th></th>';

				foreach ($this->week_days as $day) {
						
					$debug .= "<th> $day </th>";

				}

			$debug .= '</tr>';	


			if ($this->config['work_by'] === 'week_day') {

				foreach ($this->hours as $hour) {
					
					$debug .= '<tr>';

						$debug .= "<td>$hour</td>";

						foreach ($this->week_days_numeric as $day) {
							
							$debug .= '<td>';

								$debug .= "['$day']['$hour']";

							$debug .= '</td>';
						}

					$debug .= '</tr>';
				}


			} elseif($this->config['work_by'] === 'month_day') {

				foreach ($this->hours as $hour) {
					
					$debug .= '<tr>';

						$debug .= "<td>$hour</td>";

						foreach ($this->dates as $date) {
							
							$debug .= '<td>';

								$debug .= "['$date']['$hour']";

							$debug .= '</td>';
						}

					$debug .= '</tr>';
				}

			} else {

				foreach ($this->hours as $hour) {
					
					$debug .= '<tr>';

						$debug .= "<td>$hour</td>";

						$i = 0;

						foreach ($this->dates as $date) {
							
							$day = $this->week_days_numeric[$i];

							$debug .= '<td>';

								$debug .= "['$date']['$day']['$hour']";

							$debug .= '</td>';

							$i++;
						}
						

					$debug .= '</tr>';
				}

			}

			$debug .= '</table>'; 

			return $debug;
	}

    /**
     * @param array $cells
     */
	public function fill($cells)
	{
		
		try {
		
			if (!is_array($settings)) {
	                    
	            throw new \Exception("Parameter must be a array");
	                    
	        }
	        
			$this->cells = array_replace_recursive($this->cells, array_intersect_key( $cells , $this->cells));
			
		} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
		}

	}
	
    /**
     * @return string
     */
	public function getHtml()
	{
		/* Start Table */

		/* Head */

			if ($this->config['caption_content']) {

				$schedule = '<table '.$this->config['table_attributes'].'><caption '.$this->config['caption_attributes'].' >'.$this->config['caption_content'].'</caption><thead '. $this->config['thead_attributes'] .'>';

			} else {

				$schedule = '<table '.$this->config['table_attributes'].'><thead '. $this->config['thead_attributes'] .'>';
			}

			if ($this->config['work_by'] !== 'week_day') {

				$schedule .= '<tr '. $this->config['month_days_attributes'] .'> <th></th>';

					foreach ($this->dates as $date) {

						$schedule .= '<th>' . $this->date->toShow($date) . '</th>';
					}

				$schedule .= '</tr>';	
			}

			if (($this->config['work_by'] === 'week_day') || $this->config['show_week_days']) {

				$schedule .= '<tr '. $this->config['week_days_attributes'] .'> <th></th>';

					foreach ($this->week_days as $day) {

						$schedule .= "<th> $day </th>";

					}

				$schedule .= '</tr>';	
			}

		/* End Head*/

		/* Body */

			$schedule .= '</thead><tbody '. $this->config['tbody_attributes'] .'>';

			if ($this->config['work_by'] === 'week_day') {

				foreach ($this->hours as $hour) {
					
					if ($this->config['show_hour_interval']) {

						$hour_interval = $this->time->toShow($this->time->sum($hour,'00:30:00'));
						$schedule .= "<tr><td ". $this->config['hours_attributes'] .">".$this->time->toShow($hour)." <br> $hour_interval</td>";

					} else {

						$schedule .= "<tr><td ". $this->config['hours_attributes'] .">".$this->time->toShow($hour)."</td>";
					}

					foreach ($this->week_days_numeric as $day) {
							
						$schedule .= "<td ".$this->cells[$day][$hour]['attributes']." data-td='$day/$hour'>".$this->cells[$day][$hour]['content']."</td>"; 
					}

					$schedule .= '</tr>';
				} 

			} elseif($this->config['work_by'] === 'month_day') {

				foreach ($this->hours as $hour) {

					if ($this->config['show_hour_interval']) {

						$hour_interval = $this->time->toShow($this->time->sum($hour,'00:30:00'));
						$schedule .= "<tr><td ". $this->config['hours_attributes'] .">".$this->time->toShow($hour)." <br> $hour_interval</td>";

					} else {

						$schedule .= "<tr><td ". $this->config['hours_attributes'] .">".$this->time->toShow($hour)."</td>";
					}

						foreach ($this->dates as $date) {
							
							$schedule .= "<td ".$this->cells[$date][$hour]['attributes']." data_td='$date/$hour'>".$this->cells[$date][$hour]['content']."</td>";
						}

					$schedule .= '</tr>';
				}

			} else {

				foreach ($this->hours as $hour) {

						if ($this->config['show_hour_interval']) {

							$hour_interval = $this->time->toShow($this->time->sum($hour,'00:30:00'));
							$schedule .= "<tr><td ". $this->config['hours_attributes'] .">".$this->time->toShow($hour)." <br> $hour_interval</td>";

						} else {

							$schedule .= "<tr><td ". $this->config['hours_attributes'] .">".$this->time->toShow($hour)."</td>";
						}

						$i = 0;

						foreach ($this->dates as $date) {
							
							$day = $this->week_days_numeric[$i];

							$schedule .= "<td ".$this->cells[$date][$day][$hour]['attributes']." data-td='$date/$day/$hour'>".$this->cells[$date][$day][$hour]['content']."</td>"; // Muda para [$dia][$data][$hora]

							$i++;
						}
						

					$schedule .= '</tr>';
				}

			}

		/* End Body */

			$schedule .= '</tbody></table>'; 

		/* End Table */

		return $schedule;
	}
	
	/**
     * @return string
     */
	public function getJson()
	{
		return json_encode($this->cells);
	}

}