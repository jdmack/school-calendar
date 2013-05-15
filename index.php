<?php
    date_default_timezone_set("America/Los_Angeles");

    $start_date = "2013-03-31";
    $end_date = "2013-06-15";
    $assignment_file = "assignments.txt"; 
    $quarter_title = "Spring 2013";
    $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    $quarter_dates = createDateRangeArray($start_date,$end_date);
    $today_date = new DateTime();

    $assignments = array();
    $holidays = array();
?>
<html>
<head>
  <title>Spring 2013 Calendar</title>
  <link rel="stylesheet" type="text/css" href="main.css">
  <link rel="shortcut icon" href="favicon.ico" />
</head>
<?php

    // INPUT ASSIGNMENTS
    $handle = fopen($assignment_file, "r");
    $assignment_pattern = '/^([a-zA-Z0-9 ]+);([a-zA-Z0-9 #]+);([a-zA-Z]+);(\d\d\d\d)-(\d\d)-(\d\d)/';
    $holiday_pattern = '/^Holiday;([a-zA-Z ]+);(\d\d\d\d)-(\d\d)-(\d\d)/';
    while($line = fgets($handle)) {
        if(substr(trim($line), 0, 1) == '#') {
            continue;
        }
        if(preg_match($assignment_pattern, $line, $matches)) {
        
            $date_string = "$matches[4]-$matches[5]-$matches[6]";
            $this_assignment = new Assignment($matches[1], $matches[3], $matches[2], $date_string);

            if(array_key_exists($date_string, $assignments)) {
                array_push($assignments[$date_string], $this_assignment);
            }
            else {
                $assignments[$date_string] = array($this_assignment);
            }
        }
        else if(preg_match($holiday_pattern, $line, $matches)) {
            print_r($matches); 
            $date_string = "$matches[2]-$matches[3]-$matches[4]";

            if(array_key_exists($date_string, $holidays)) {
                array_push($holidays[$date_string], $matches[1]);
            }
            else {
                $holidays[$date_string] = array($matches[1]);
            }
        }
        else {
            continue;
        }

    }

?>

<body background="#000000">
  <center>
    <h1>
      <?php print "$quarter_title\n"; ?>
      <br>
    </h1>

    <table border="4" align="center">
      <tr>
        <?php
            // PRINT COLUMN HEADERS
            foreach($days_of_week as $day) {
            print "<th class=\"dayHeader\">$day</th>\n";
            }
        ?>
      </tr>
<?php
   // BUILD CALENDAR
    $weeks = 0;
    foreach($quarter_dates as $this_date_str) {

        $this_date = new DateTime($this_date_str);
        $week_day  = $this_date->format("w");
        $day       = $this_date->format("j");
        $month     = $this_date->format("F");
        $year      = $this_date->format("Y");
        $padded_day       = $this_date->format("d");
        $padded_month     = $this_date->format("m");
        $padded_year      = $this_date->format("Y");

        $date_string = "$padded_year-$padded_month-$padded_day";

        if($week_day == 0) {
            $weeks++;
            echo "<tr>\n";
        }

        if($this_date->format("Y-m-d") < $today_date->format("Y-m-d")) {
            echo "<td id=\"$date_string\" class=\"past\">";
        }
        else if($this_date->format("Y-m-d") == $today_date->format("Y-m-d")) {
            echo "<td id=\"$date_string\" class=\"today\">";
        }
        else {
            echo "<td id=\"$date_string\">";
        }

        if($week_day == 0) {
            echo "<br><br><h2 class=\"week\">Week $weeks</h2>\n";
        }
        else {
            if($day == 1) {
                echo "<b><span class=\"month\">$month</span> </b>";
            }

            echo "<b>$day</b><br><br>";
        }

        // HOLIDAY LOOP
        if(array_key_exists($date_string, $holidays)) {
            foreach($holidays[$date_string] as $this_holiday) {
                print "<span class=\"holiday\"><h3>$this_holiday</h3></span><br>\n";
            }
        }

        // ASSIGNMENT LOOP
        if(array_key_exists($date_string, $assignments)) {
            foreach($assignments[$date_string] as $this_assignment) {
                print "<span class=\"$this_assignment->ass_type\">$this_assignment->ass_class: $this_assignment->ass_name</span><br>\n";
            }
        }

        echo "</td>\n";

        if($week_day == 6) {
            echo "</tr>\n";
        }
    }
?>

    </table>
  </center>
</body>
</html>



<?php
    function createDateRangeArray($strDateFrom,$strDateTo)
    {
        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }


    class Assignment
    {
        public $ass_class;
        public $ass_type;
        public $ass_name;
        public $ass_date;

        function __construct($cl, $ty, $na, $da) {
            $this->ass_class = $cl;
            $this->ass_type  = $ty;
            $this->ass_name  = $na;
            $this->ass_date  = $da;
        }

        function getHTMLString()
        {
            return "<span class=\"$type\">$class: $name</span><br>\n";
        }
    }




?>
