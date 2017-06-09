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
	private $calendar_week_days_excluded;

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
					'end_date' => DateMachine::addDays(date("Y-m-d"), 20, false),
					'show_week_days' => true,
					'show_month_days' => true,
					'language' => 'PT',
					'start_hour' => '00:00:00',
					'end_hour' => '23:59:59',
					'work_by' => 'all',
					'calendar_start_week_day_number' => 1,
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
			$this->date->config( ['show_format' => $this->config['show_date_format'], 'language' => $this->config['language'] ] );
			$this->time->config( ['show_format' => $this->config['show_time_format'], 'language' => $this->config['language'] ] );
			
			if ($this->config['work_by'] === 'calendar') { 
				
				$this->calendar_week_days_excluded = $this->date->getUntilWeekDayBefore($this->config['start_date'], $this->config['calendar_start_week_day_number']);
				
				array_pop($this->calendar_week_days_excluded);
				
				$this->dates = $this->date->interval( $this->date->getUntilWeekDayBefore($this->config['start_date'], $this->config['calendar_start_week_day_number'], false), $this->config['end_date'] );
				$this->week_days = $this->date->weekDaysBetween( $this->date->getUntilWeekDayBefore($this->config['start_date'], $this->config['calendar_start_week_day_number'], false), $this->config['end_date'], 'name');
				$this->week_days_numeric = $this->date->weekDaysBetween( $this->date->getUntilWeekDayBefore($this->config['start_date'], $this->config['calendar_start_week_day_number'], false), $this->config['end_date']);
				
			} else {
			
				$this->dates = $this->date->interval( $this->config['start_date'], $this->config['end_date'] );
				$this->week_days = $this->date->weekDaysBetween( $this->config['start_date'], $this->config['end_date'], 'name');
				$this->week_days_numeric = $this->date->weekDaysBetween( $this->config['start_date'], $this->config['end_date']);
			
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

				foreach ($this->week_days_numeric as $day) {
				
					foreach ($this->hours as $hour) {
						
						$cells[$day][$hour] = ['content' => '', 'attributes' => ''];
					}

				}


			} elseif ($this->config['work_by'] === 'month_day') {

				foreach ($this->dates as $date) {
				
					foreach ($this->hours as $hour) {
						
						$cells[$date][$hour] = ['content' => '', 'attributes' => ''];
					}

				}

			} elseif ($this->config['work_by'] === 'calendar') {
				
				foreach ($this->dates as $date) {

					$cells[$date][$this->date->weekDay($date)] = ['content' => '', 'attributes' => ''];
				}
				
			} else { 

				foreach ($this->dates as $date) {
				
					foreach ($this->week_days_numeric as $day) {
						
						foreach ($this->hours as $hour) {
							
							$cells[$date][$day][$hour] = ['content' => '', 'attributes' => ''];
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
			
			if ($this->config['work_by'] === 'calendar') {
				
				$week_days = array_chunk($this->week_days,7);
		
				$debug .= '<tr '. $this->config['week_days_attributes'] .'>';
		
				foreach ($week_days[0] as $day) {
					
					$debug .= "<th> $day </th>";
		
				}
				
				$debug .= '</tr>';	
				
			} else {
	
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
			
			}

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


			} elseif ($this->config['work_by'] === 'month_day') {

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

			} elseif ($this->config['work_by'] === 'calendar') {
				
		
				$weeks = array_chunk($this->dates,7);
				$week_days_numeric = array_chunk($this->week_days_numeric,7);
				
				//var_dump($this->calendar_week_days_excluded);
				
				foreach ($weeks as $week_key => $week) {
					
					$debug .= "<tr>";
					
					foreach ($week as $day_key => $day) {
						
						if (!in_array($day, $this->calendar_week_days_excluded, true)) {
														// Data / Dia da semana numérico
							$debug .= "<td>"."['$day']['". $week_days_numeric[$week_key][$day_key]."']"."</td>";
						
						} else {
							
							$debug .= "<td></td>";
						}
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
	
	private function getHtmlTrTh()
	{
		$schedule = '';
		
		if ($this->config['work_by'] !== 'week_day' && $this->config['show_month_days'] !== false) {
			
			$schedule .= '<tr '. $this->config['month_days_attributes'] .'> <th></th>';

				foreach ($this->dates as $date) {

					$schedule .= '<th>' . $this->date->toShow($date) . '</th>';
				}

			$schedule .= '</tr>';	
		}

		if (($this->config['work_by'] === 'week_day') || $this->config['show_week_days'] === true) {

			$schedule .= '<tr '. $this->config['week_days_attributes'] .'> <th></th>';

				foreach ($this->week_days as $day) {

					$schedule .= "<th> $day </th>";

				}

			$schedule .= '</tr>';	
		}
		
		return $schedule;
	}
	
	private function getHtmlTrThCalendar()
	{
		$schedule = '';
		
		$week_days = array_chunk($this->week_days,7);
		
		$schedule .= '<tr '. $this->config['week_days_attributes'] .'>';

		foreach ($week_days[0] as $day) {

			$schedule .= "<th> $day </th>";

		}

		$schedule .= '</tr>';	
		
		return $schedule;
	}
	
	private function getHtmlTrTdWeekDay()
	{
		$schedule = '';
		
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
				
		return $schedule;
	}
	
	private function getHtmlTrTdMonthDay()
	{
		$schedule = '';
		
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
				
		return $schedule;
	}

	private function getHtmlTrTdCalendar()
	{
		$schedule = '';
		
		$weeks = array_chunk($this->dates,7);
		$week_days_numeric = array_chunk($this->week_days_numeric,7);
		
		foreach ($weeks as $week_key => $week) {
			
			$schedule .= "<tr>";
			
			foreach ($week as $day_key => $day) {
				
				if (!in_array($day, $this->calendar_week_days_excluded, true)) {
												// Data / Dia da semana numérico
					$schedule .= "<td ".$this->cells[$day][$week_days_numeric[$week_key][$day_key]]['attributes']." data-td='$day/".$week_days_numeric[$week_key][$day_key]."'>".$this->cells[$day][$week_days_numeric[$week_key][$day_key]]['content']."</td>";
				
				} else {
					
					$schedule .= "<td></td>";
				}	
			}
			
			$schedule .= '</tr>';
		}
				
		return $schedule;
	}

	private function getHtmlTrTdDefault()
	{
		$schedule = '';
		
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
				
		return $schedule;
	}
	
    /**
     * @return string
     */
	public function getHtml()
	{
		/* Start Table */

		/* Head */
			
		$tr_th = ($this->config['work_by'] === 'calendar') ? $this->getHtmlTrThCalendar() : $this->getHtmlTrTh();

		if ($this->config['caption_content']) {

			$schedule = '<table ' . $this->config['table_attributes'] . '><caption ' . $this->config['caption_attributes'].' >' . $this->config['caption_content'] .'</caption><thead '. $this->config['thead_attributes'] . '>' . $tr_th;

		} else {

			$schedule = '<table ' . $this->config['table_attributes'] . '><thead ' . $this->config['thead_attributes'] .'>' .  $tr_th;
		}

		/* End Head*/

		/* Body */


		if ($this->config['work_by'] === 'week_day') {

			$schedule .= '</thead><tbody '. $this->config['tbody_attributes'] .'>' . $this->getHtmlTrTdWeekDay() . '</tbody></table>';

		} elseif ($this->config['work_by'] === 'month_day') {

			$schedule .= '</thead><tbody '. $this->config['tbody_attributes'] .'>' . $this->getHtmlTrTdMonthDay(). '</tbody></table>';

		} elseif ($this->config['work_by'] === 'calendar') { 
			
			$schedule .= '</thead><tbody '. $this->config['tbody_attributes'] .'>' . $this->getHtmlTrTdCalendar(). '</tbody></table>';
			
		} else {

			$schedule .= '</thead><tbody '. $this->config['tbody_attributes'] .'>' . $this->getHtmlTrTdDefault(). '</tbody></table>';
		}

		/* End Body */


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