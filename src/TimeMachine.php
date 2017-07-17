<?php

/**
*
* @author Henrique Fernandez Teixeira
*
* This is a set of functions that makes easily work with hours in format HH:MM:SS.
*     
*/

namespace Blacktools\DateTime;

use Blacktools\DateTime\ErrorHandler;

class TimeMachine
{

    private $config;

    /**
     * @param array $settings
     */
    public function __construct($settings = [])
    {

        $this->config = [

                'show_format' => 'H:i:s'
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
                    
                throw new \Exception("Config parameter must be a array");
            }

            $this->config = array_replace($this->config, array_intersect_key($settings, $this->config));

        } catch(\Exception $e) {
            
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
            
            if (!is_string($value) || !is_string($from) || !is_string($to)) {
                
                 throw new \Exception("<p> All the parameters must be a string given: $value, $from, $to</p>");
            }
            
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
     * @param string $time
     *
     * @return boolean
     */
    public function isShow($time = '')
    {

        $d_config = date_create_from_format($this->config['show_format'], $time);

        return ($d_config)? true : false ;

    }
    
    /**
     * @param string $time
     *
     * @return boolean
     */
    public function isWork($time = '')
    {

        $d_config = date_create_from_format('H:i:s', $time);

        return ($d_config)? true : false ;

    }

    /**
     * @param string $time
     *
     * @return string|null
     */
    public function toShow($time = '')
    {
        try {            

            $time_obj = date_create_from_format('H:i:s', $time);

            if (!$time_obj) {

                throw new \Exception("<p> Work time format is: 'H:i:s' passed: $time . </p>");    

            } else {

                return date_format($time_obj, $this->config['show_format']);
            }


        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
        }
    }

    /**
     * @param string $class
     *
     * @return string|null
     */
    public function toWork($time = '')
    {
        try { 

            $time_obj = date_create_from_format($this->config['show_format'], $time);

            if (!$time_obj) {

                throw new \Exception("<p> Config show time format is: " . $this->config['show_format'] . " passed: $time . </p>");    

            } else {

                return date_format($time_obj, 'H:i:s');
            }


        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
        }

    }

    /**
     * @param string $class
     *
     * @return boolean
     */
    public static function validate($time)
    {

        if (!is_string($time)) {

            return false;
        }

        if (!date_create_from_format('H:i:s', $time)) {

            return false;

        } else {
            
            $time = explode(':', $time);
            
            /* [0] Hour / [1] Minutes / [2] Seconds */
            
            if (!is_numeric($time[0]) OR !is_numeric($time[1]) OR !is_numeric($time[2])) {

                return false;

            } elseif ( ($time[0] > 23) OR ($time[1] > 59) OR ($time[2] > 59) ) {

                return false;

            } elseif ( ($time[0] < 0) OR ($time[1] < 0) OR ($time[2] < 0) ) {

                return false;
            }

        }

        return true;
    }

    /**
     * @param string $time
     *
     * @return string|null
     */
    public static function toSeconds($time)
    {

        try {
            
            if (!TimeMachine::validate($time)) {

                throw new \Exception("<p> Time must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $time, $second </p>");
            }
            
            $time = date_parse($time);
            
            $this->reset();
            
            return $time['hour'] * 3600 + $time['minute'] * 60 + $time['second'];

        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
        }

    }

    /**
     * @param string $first
     * @param string $second
     * @param string $comparison
     *
     * @return boolean|null
     */
    public static function compare($first, $second, $comparison = '==')
    {

        try {
                
            if (!TimeMachine::validate($first) || !TimeMachine::validate($second)) {

                throw new \Exception("<p> Time interval must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $second </p>");
            };

            /* Start function */

            $aux = date_parse($first);
            $first = $aux['hour'] * 3600 + $aux['minute'] * 60 + $aux['second'];

            $aux = date_parse($second);
            $second = $aux['hour'] * 3600 + $aux['minute'] * 60 + $aux['second'];


            switch ($comparison) {

                case '>': 
                    return $first > $second; 
                    break;

                case '<': 
                    return $first < $second; 
                    break;

                case '==': 
                    return $first == $second; 
                    break;

                case '>=': 
                    return $first >= $second; 
                    break;

                case '<=': 
                    return $first <= $second; 
                    break;

                case '!=': 
                    return $first != $second; 
                    break;

                default: 
                    throw new \Exception("<p> Comparison must be '>', '<', '==', '>=', '<=' or '!=', you passed: $comparison </p>");
                    break;
            }

        } catch(\Exception $e) { 

            ErrorHandler::displayError($e);
            
            return null;
        }


    }

    /**
     * @param string $first
     * @param string $second
     * @param string $time 
     * 
     * @return array|null
     */
    public function intervalShow($first, $second, $time = '01:00:00')
    {
        try {
                
            if (!TimeMachine::validate($first) || !TimeMachine::validate($time) || !TimeMachine::validate($second)) {

                throw new \Exception("<p> Time must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $time, $second </p>");
            }

            if($time == '00:00:00'){

                throw new \Exception("<p> Interval can't be 00:00:00 </p>");                
            }

            $interval = [];
            $i = 0;
            $interval[0]['show'] = date_format(date_create_from_format('H:i:s', $first), $this->config['show_format']);; 
            $interval[0]['work'] = $first;

            while (TimeMachine::compare($first, $second, '<')) {
                
                $i++;
                
                $first = TimeMachine::sum($first, $time);

                if($first == '00:00:00'){

                    break;
                }

                $interval[$i]['show'] = $this->toShow($first);
                $interval[$i]['work'] = $first;
            }

            return $interval;


        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
        }
        
        
    }


    /**
     * @param string $first
     * @param string $second
     * @param string $time
     *
     * @return array
     */
    public static function interval($first, $second, $time = '01:00:00')
    {
        try {
                
            if (!TimeMachine::validate($first) || !TimeMachine::validate($time) || !TimeMachine::validate($second)) {

                throw new \Exception("<p> Time must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $time, $second </p>");
            }

            if($time == '00:00:00'){

                throw new \Exception("<p> Interval can't be 00:00:00 </p>");                
            }

            $interval = [];
            $interval[] = $first;

            while (TimeMachine::compare($first, $second, '<')) {

                $first = TimeMachine::sum($first, $time);

                if($first == '00:00:00'){

                    break;
                }

                $interval[] = $first;

            }

            return $interval;


        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
        }

    }

    /**
     * @param string $first
     * @param string $second
     *
     * @return string|null
     */
    public static function diff($first, $second)
    {
       try {
                
            if (!TimeMachine::validate($first) || !TimeMachine::validate($second)) {

                throw new \Exception("<p> Time must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $time, $second </p>");
            }

            if(TimeMachine::compare($first, $second, '<')){

                throw new \Exception("<p> The second paremeter must be smaller than the fist, you passed: $first, $second</p>");
            }

            $second = strtotime($second);

            $first = strtotime($first);
                                
            if ($first < $second) {

                $first += 86400;
            }
                                
            return date("H:i:s", strtotime("00:00:00") + ($first - $second)); 

        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;
        }

    }

    /**
     * @param string 
     * 
     * @return string
     */
    public static function sum()
    {

        try {
                
            /* Get func args */

            $times = [];

            $num_args = func_num_args();

            if ($num_args > 24) {

                throw new \Exception("<p> Can't sum more than 24 values </p>");                
            }

            for ($i=0; $i < $num_args; $i++) {

                if (!TimeMachine::validate(func_get_arg($i))) {

                    throw new \Exception("<p> Time must be in format 'HH:MM:SS' / '23:59:59' , you passed: " . func_get_arg($i) ."</p>");
                };

                $times[] = func_get_arg($i);
            };

            if ($num_args < 2) {

                throw new \Exception('<p> Few values to do a sum: ' . $num_args . '</p>' );
                    
            }

            /* Start function */

            $result = array_reduce($times, function($first,$second){

                    $first = date_parse($first);

                    $second = date_parse($second);

                    $final = '';
                    $rest = 0;
                            
                    /* Seconds */
                            
                    $val = $first['second'] + $second['second'];
                            
                    if (($val - 60) >= 0) {
                            
                        $rest = 1;
                            
                        $val = $val - 60;
                            
                    }
                            
                    $final = ($val < 10)? ':0' . (string)$val : ':' . (string)$val ;
                            
                            
                    /* Minutes */
                            
                    $val = $first['minute'] + $second['minute'] + $rest;
                            
                    $rest = 0;
                            
                    if (($val - 60) >= 0) {
                            
                        $rest = 1;
                            
                        $val = $val - 60;
                            
                    }
                            
                    $final = ($val < 10)? ':0' . (string)$val . $final : ':' . (string)$val . $final;
                            
                            
                    /* Hours */
                            
                    $val = $first['hour'] + $second['hour'] + $rest;
                            
                    if ($val > 23) {
                            
                        $final = '00:00:00';
                            
                    } else {
                            
                        $final = ($val < 10)? '0' . (string)$val . $final : (string)$val . $final;
                            
                    }

                    return $final;

            });

            return $result;
                

        } catch(\Exception $e) {

            ErrorHandler::displayError($e);
            
            return null;

        }
            
    }
    
} 

    
