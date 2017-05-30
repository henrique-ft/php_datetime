<?php

namespace Blacktools\DateTime;

class ErrorHandler
{
    public static function displayErrorAndDie(\Exception $e)
    {
        if (error_reporting() !== 0) {
            
            echo '<pre style="
                        background-color: rgba(0, 0, 0, 0.68);
                        border: 5px solid #000000;
                        border-radius: 15px;
                        padding: 10px;
                        color: white;
                    ">';    
            
            echo $e->getTraceAsString();
            
            echo $e->getMessage(); 
            
            echo '</pre>';
            
            die();  
        } 
    }
    
    public static function displayError(\Exception $e)
    {
        if (error_reporting() !== 0) {
            
            echo '<pre style="
                        background-color: rgba(0, 0, 0, 0.68);
                        border: 5px solid #000000;
                        border-radius: 15px;
                        padding: 10px;
                        color: white;
                    ">';
            
            echo $e->getTraceAsString();
            
            echo $e->getMessage(); 
            
            echo '</pre>';
        }
    }
}