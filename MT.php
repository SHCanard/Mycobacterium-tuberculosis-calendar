<?php
//gets datas from post method
$startDate=$_POST['start'];
$endDate=$_POST['end'];
$lstHolidays=$_POST['holidays'];

//separates each holiday
$holidays=explode(',',$lstHolidays);

$startStamp=strtotime($startDate)-84*86400; //creation date can be up to 84 days before start of calendar
$start=strtotime($startDate);
$endStamp=strtotime($endDate)-84*86400; //only takes in count creations 84 days before end of calendar

//$holidays=array("24/12/2011","25/12/2011","31/12/2011","01/01/2012","09/04/2012","30/04/2012","01/05/2012","08/05/2012","17/05/2012","18/05/2012","28/05/2012","14/07/2012","15/08/2012","24/09/2012","01/11/2012","02/11/2012","11/11/2012","24/12/2012","25/12/2012");

header("Content-type: application/vnd.ms-excel"); 
header("Content-disposition: attachment; filename=\"MT_calendar.csv\"");

$csv = "Reading date;Creation date;Sowing date;Day;Quant"."\n"; //row names

for ($i=$startStamp;$i<=$endStamp;$i=$i+86400)//increments day of creation
	{
	daysCalculation($i,5); //calculates all dates for 5 days after creation
	daysCalculation($i,9);
	daysCalculation($i,14);
	daysCalculation($i,21);
	daysCalculation($i,28);
	daysCalculation($i,56);
	daysCalculation($i,84); //calculates all dates for 84 days after creation
	}

function daysCalculation($i,$j){
	global $holidays;
	global $start;
	global $csv;
	
	$timestamp=$i;
	
	$sowingDay=date("d/m/Y",$timestamp);

	$f=0;
	
	$sowingDayName=date("l",$timestamp);	
	
	if ($sowingDayName=="Sunday") //sunday is not a working day, skip to monday
		{
		$timestamp=$timestamp+86400;
		$sowingDay=date("d/m/Y",$timestamp);
		$f=1;
		}
	elseif ($sowingDayName=="Saturday") //saturday is not a working day, skip to monday
		{
		$timestamp=$timestamp+2*86400;
		$sowingDay=date("d/m/Y",$timestamp);
		$f=2;
		}
	
	$k=0;
		
	while ($k<count($holidays)) //reads holidays list
		{
		if ($holidays[$k]==$sowingDay)
			{
			$timestamp=$timestamp+86400;
			$sowingDay=date("d/m/Y",$timestamp);
			$k=0;//back to first holiday
			$f++;
			}
		else
			$k++;//next holiday entry
		}

	$sowingDayName=date("l",$timestamp);
	
	if ($sowingDayName=="Sunday")
		{
		$timestamp=$timestamp+86400;
		$sowingDay=date("d/m/Y",$timestamp);
		$f++;
		}
	elseif ($sowingDayName=="Saturday")
		{
		$timestamp=$timestamp+2*86400;
		$sowingDay=date("d/m/Y",$timestamp);
		$f=$f+2;
		}
		
	$creationDay=date("d/m/Y",$i);
	
	$quant=date("z",$i)+1;
	
	$reading=$timestamp+$j*86400+$f*86400;

	$diff=$timestamp-$i;
	
	$reading=$reading-$diff;

	$readingDayName=date("l",$reading);
	
	if ($readingDayName=="Sunday")
		{
		$reading=$reading+86400;
		}
	elseif ($readingDayName=="Saturday")
		{
		$reading=$reading+2*86400;
		}
	
	$readingDay=date("d/m/Y",$reading);
	
	$k=0;
	while ($k<count($holidays))
		{
		if ($holidays[$k]==$readingDay)
			{
			$reading=$reading+86400;
			$readingDay=date("d/m/Y",$reading);
			$k=0;
			}
		else
			$k++;
		}
	$readingDayName=date("l",$reading);
	
	if ($readingDayName=="Sunday")
		{
		$reading=$reading+86400;
		}
	elseif ($readingDayName=="Saturday")
		{
		$reading=$reading+2*86400;
		}
		
	if ($reading>=$start)
		{
		$readingDay=date("d/m/Y",$reading);
		$csv.= $readingDay.";".$creationDay.";".$sowingDay.";D";
		if ($j<10)
			$csv.= "0";
		$csv.= $j.";".$quant."\n";
		}
}
print($csv);
exit;
?>
