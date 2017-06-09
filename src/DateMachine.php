<?php

/**
*
* @author Henrique Fernandez Teixeira
*
* This is a set of functions that makes easily work with date in format Y-m-d.
*     
*/

namespace Blacktools\DateTime;

use Blacktools\DateTime\ErrorHandler;

class DateMachine
{

	private $languages;
	private $config;
	
    /**
     * @param array $settings
     */
	public function __construct($settings = [])
	{

		$this->config = [

				'show_format' => 'Y-m-d',
				'language' => 'EN',
				'timezone' => 'America/Sao_Paulo'
			];

		$this->languages = [ 


					'PT' => [

								'days' => ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
								'months' => ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho','agosto','setembro','outubro','novembro','dezembro']

							],
							
					'EN' => [

								'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
								'months' => ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']

							],
							
					'ES' => [

								'days' => ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
								'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre']

							]

			];

		$this->config($settings);

	}
	
    /**
     * @param array $settings
     */	
	public function config($settings)
	{
		
		try {

			if (!is_array($settings)) {
				
				throw new \Exception("<p> Config parameter must be a array </p>");
				
			}

			$this->config = array_replace($this->config, array_intersect_key($settings, $this->config));

			date_default_timezone_set($this->config['timezone']);

		} catch (\Exception $e) {
			
	        ErrorHandler::displayErrorAndDie($e);
		}
	}

    /**
     * @param array $params
     *
     * @return string
     */
	public static function format($value, $from, $to)
	{
		try {

			$date_obj = date_create_from_format($from, $value);

			if (!$date_obj) {

				throw new \Exception("<p> Value you intent to convert and initial format passed are different. Value:".$value." Format:".$from."</p>");	

			} else {

				return date_format($date_obj, $to);
			}

		} catch (\Exception $e) {
			
	        ErrorHandler::displayError($e);

            return null;
		}
	}

    /**
     * @param string $date
     *
     * @return boolean
     */
	public function isShow($date)
	{

		$d_config = date_create_from_format($this->config['show_format'], $date);

		return ($d_config)? true : false ;

	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */
	public function isWork($date)
	{

		$d_config = date_create_from_format('Y-m-d', $date);

		return ($d_config)? true : false ;

	}
	
    /**
     * @param string $date
     *
     * @return string|null
     */
	public function toShow($date)
	{
		
		try {			

			$date_obj = date_create_from_format('Y-m-d', $date);

			if (!$date_obj) {

				throw new \Exception("<p> Work date format is: 'Y-m-d', '9999-12-31' passed: $date . </p>");	

			} else {

				return date_format($date_obj, $this->config['show_format']);
			}


		} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
		}
	}
	
    /**
     * @param string $date
     *
     * @return string|null
     */
	public function toWork($date)
	{
		
		try {

			$date_obj = date_create_from_format($this->config['show_format'], $date);

			if (!$date_obj) {

				throw new \Exception("<p> Config show date format is: " . $this->config['show_format'] . " passed: $date . </p>");	

			} else {

				return date_format($date_obj, 'Y-m-d');
			}


		} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
		}

	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */	
	public static function validate($date)
	{
		
        if (!is_string($date)) {

            return false;
        }

        if (!date_create_from_format('Y-m-d', $date)) {

            return false;

        } else {
            
            $date = explode('-', $date);
            
            /* [0] Year / [1] Month / [2] Days */
            
            if (!is_numeric($date[0]) OR !is_numeric($date[1]) OR !is_numeric($date[2])) {

                return false;

            } elseif ( ($date[0] > 9999) OR ($date[1] > 12) OR ($date[2] > 31) ) {

                return false;

            } elseif ( ($date[0] < 1) OR ($date[1] < 1) OR ($date[2] < 1) ) {

                return false;
            }

        }

        return true;		
		
	}
	
    /**
     * @param string $start_date
     * @param number $days
     * @param boolean $interval
     *
     * @return array|string|null
     */
	public static function addDays($start_date, $days = 7, $interval = true)
	{
		try {

			if (!DateMachine::validate($start_date)) {

				throw new \Exception("<p> You passed value out from the work format 'Y-m-d', '9999-12-31' : $start_date</p>");			
			}

			if ($days === 0 || $days === '0' || $days === null) {

				throw new \Exception("<p> Days can't be 0 or null, you passed: $days</p>");				
			}
			
			if ($days < 0) {

				DateTime::subDays($start_date, abs($days), $interval);				
			}
    
			$dates = array();

			$date = date_create($start_date);
	        $dates[] = $start_date;
	            
	        for ($i=0; $i < $days ;$i++) {
	                
	            if ($i != 0) {
	                    
	                $date = date_create($current_date);
	            }
	            
	            date_add($date, date_interval_create_from_date_string("1 day"));
	            $current_date = date_format($date, "Y-m-d");

	            if ($interval) {

	            	$dates[] = $current_date;
	        	}
	        }

	     	return ($interval)? $dates : $current_date;

     	} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
     	}
	}

    /**
     * @param string $start_date
     * @param number $days
     * @param boolean $interval
     *
     * @return array|string|null
     */
	public static function subDays($start_date, $days = 7, $interval = true)
	{
		try {

			if (!DateMachine::validate($start_date)) {

				throw new \Exception("<p> You passed value out from the work format 'Y-m-d', '9999-12-31' : $start_date</p>");			
			}

			if ($days < 1) {

				throw new \Exception("<p> Days can't be 0 or negative, you passed: $days</p>");				
			}
     
			$dates = array();

			$date = date_create($start_date);
	            
	        for ($i=0; $i < $days ;$i++) {
	                
	            if ($i != 0) {
	                    
	                $date = date_create($current_date);
	            }
	            
	            date_sub($date, date_interval_create_from_date_string("1 day"));
	            $current_date = date_format($date, "Y-m-d");

	            if ($interval) {

	            	$dates[] = $current_date;
	        	}
	        }
	        
			$dates = array_reverse($dates);
			
			$dates[] = $start_date;

	     	return ($interval)? $dates : $current_date;

     	} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
     	}
	}

    /** date_format($current_date, "Y-m-d") == $day
     * @param string $start_date
     * @param number $days
     * @param boolean $interval
     *
     * @return array|string|null
     */
	public static function getUntilWeekDayBefore($start_date, $day = 7, $interval = true)
	{
		try {

			if (!DateMachine::validate($start_date)) {

				throw new \Exception("<p> You passed value out from the work format 'Y-m-d', '9999-12-31' : $start_date</p>");			
			}

			$week_days = [0,1,2,3,4,5,6,7];

			if (!in_array($day, $week_days, true)) {

				throw new \Exception("<p> Day can be only 0,1,2,3,4,5,6 or 7 you passed: $day</p>");				
			}
     
			$dates = array();

			$date = date_create($start_date);
	            
	        for ($i=0; $i < 7 ;$i++) {
	                
	            if ($i != 0) {
	                    
	                $date = date_create($current_date);
	            }
	            
	            date_sub($date, date_interval_create_from_date_string("1 day"));
	            $current_date = date_format($date, "Y-m-d");
				
	            if ($interval) {

	            	$dates[] = $current_date;
	        	}
	        	
	        	if ((int)date_format(date_create($current_date), 'w') === (int)$day) {
	        		
	        		break;
	        	}
	        }
	        
			$dates = array_reverse($dates);
			
			$dates[] = $start_date;

	     	return ($interval)? $dates : $current_date;

     	} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
     	}
	}

    /**
     * @param string $first
     * @param string $second
     *
     * @return array|null
     */
	public static function interval($first, $second)
	{

		try{

			if (!DateMachine::validate($first) || !DateMachine::validate($second)) {

				throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $first, $second</p>");			
			}

		    $return = array();
		    $interval = new \DateInterval('P1D');

		    $end = new \DateTime($second);
		    $end->add($interval);

		    $period = new \DatePeriod(new \DateTime($first), $interval, $end);

		    foreach ($period as $date) { 
		        $return[] = $date->format('Y-m-d'); 
		    }

		    return $return;

	    } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
     	}
	}
	
    /**
     * @param string $first
     * @param string $second
     * @param string $type
     *
     * @return array|null
     */	
	public function weekDaysBetween($first = '', $second = '', $type = 'numeric')
	{

		try {
			
			if (empty($first) || empty($first)) {

				if (!$this->validate($first) || !$this->validate($second)) {

					throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $first, $second</p>");			
				}

				return $this->languages[$this->config['language']]['days'];

			} else {

				if (!$this->validate($first) || !$this->validate($second)) {

					throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $first, $second</p>");			
				}

				$interval = $this->interval($first, $second);		

				if ($type === 'name') {
						
					$callback = function($date){

						return $this->languages[$this->config['language']]['days'][date('w', strtotime($date))];
					};


				} elseif($type === 'numeric') {

					$callback = function($date){

						return date('w', strtotime($date));
					};

				} else {

					throw new \Exception("<p>  The third parameter can only be 'name' or 'numeric' </p>");
				}

			    return array_map($callback, $interval);

			}


		} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;

		}

	}
	
    /**
     * @param string $date
     * @param string $type
     *
     * @return string|int|null
     */
	public function weekDay($date, $type = 'numeric')
	{

		try {
	
			if (!$this->validate($date)) {
				
				throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $date </p>");
			}
			
			if ($type === 'name') {

				return $this->languages[$this->config['language']]['days'][date('w', strtotime($date))];


			} elseif ($type === 'numeric') {

				return date('w', strtotime($date));

			} else {

				throw new \Exception("<p>  The second parameter can only be 'name' or 'numeric', you passed: $type </p>");
			}

		} catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;

		}

	}

}
