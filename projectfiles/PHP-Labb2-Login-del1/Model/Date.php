<?php

class Date {
    //objekt för tidsvisning
    public function __construct(){
        //behöver inte någon indata så konstruktorn är tom
    }

    public function getDateTime($clearText){
        //gör en standard för tid
        $datetime = date('F');

        //kollar om parametern är tom, :
        /*
        if($clearText !== true){
            $clearText = false;
        }
        */
        //tydligen kan man inte anropa metoden med tom parameter...

        if($clearText === TRUE){
            //om true så ska vi formatera texten så at den blir enligt UC 1.1
        $dayOfWeek = date('l');
            $dayOfWeek = $this->getSwedishWeekNames($dayOfWeek); //översätter till svenska
        $dayOfMonth = date('d');
        $Month = date('F');
            $Month = $this->getSwedishMonthNames($Month); //översätter till svenska
        $year = date('Y');
        $timeAndSec = date('H:i:s');

        $datetime = $dayOfWeek . ", den " . $dayOfMonth . " " . $Month . " år " . $year . ". Klockan är [" . $timeAndSec ."]";
        }

        return $datetime;
    }

    private function getSwedishMonthNames($dayOfMonth){
        switch($dayOfMonth){ //byter ut den engelska månadsnamnen till svensk...
            case "January":
                $dayOfMonth = "Januari";
                break;
            case "February":
                $dayOfMonth = "Februari";
                break;
            case "March":
                $dayOfMonth = "Mars";
                break;
            case "April":
                $dayOfMonth = "April";
                break;
            case "May":
                $dayOfMonth = "Maj";
                break;
            case "June":
                $dayOfMonth = "Juni";
                break;
            case "July":
                $dayOfMonth = "Juli";
                break;
            case "August":
                $dayOfMonth = "Augusti";
                break;
            case "September":
                $dayOfMonth = "September";
                break;
            case "October":
                $dayOfMonth = "Oktober";
                break;
            case "November":
                $dayOfMonth = "November";
                break;
            case "December":
                $dayOfMonth = "December";
                break;
            default:
                $dayOfMonth = "error in Switch...";
        }
        return $dayOfMonth;

    }

    private function getSwedishWeekNames($dayOfWeek){
        switch($dayOfWeek){ //byter ut den engelska veckodagen till svensk...
            case "Monday":
                $dayOfWeek = "Måndag";
                break;
            case "Tuesday":
                $dayOfWeek = "Tisdag";
                break;
            case "Wednesday":
                $dayOfWeek = "Onsdag";
                break;
            case "Thursday":
                $dayOfWeek = "Torsdag";
                break;
            case "Friday":
                $dayOfWeek = "Fredag";
                break;
            case "Saturday":
                $dayOfWeek = "Lördag";
                break;
            case "Sunday":
                $dayOfWeek = "Söndag";
                break;
            default:
                $dayOfWeek = "error in Switch...";
        }

        return $dayOfWeek;
    }

}