<?php

class Schedule extends CI_Model {
	
	protected $xml = null;
	protected $days = array();
	protected $courses = null;
	protected $timeslots = null;
	
	public function __construct() {
		parent::__construct();

		$this->xml = simplexml_load_file(DATAPATH . 'schedule.xml');
		
		// create arrays for each propert
		$this->days = array();
		$this->courses = array();
		$this->timeslots = array();
		$daysoftheweek = array();
		//Days perspective
		foreach($this->xml->daysofweek as $day) {
			$dayBooking = array();
            foreach($day->day->dbooking as $b) {
                $booking = new Booking();
				$booking->course = $b->course;
				$booking->day = $day->day['name'];
				$booking->time = $b->time;
				$booking->room = $b->room;
				$booking->type = $b->type;
				$booking->instructor = $b->instructor;
				array_push($dayBooking, $booking);
             }
			 array_push($this->days, $dayBooking);
            }	
		
		foreach($this->xml->courses as $course)
		{
			$courseBookings = array();
			foreach($course->course->cbooking as $c)
			{
				$booking = new Booking();
				$booking->course = (string) $course['num'];
				$booking->day = $c->day;
				$booking->time = $c->time;
				$booking->room = $c->room;
				$booking->type = $c->type;
				$booking->instructor = $c->instructor;
				array_push($courseBookings, $booking);
			}
			array_push($this->courses, $courseBookings);
			print_r($this->courses);
		}
		
		foreach($this->xml->timeslots as $slot)
		{
			$timeBookings = array();
			foreach($slot->slots->tbooking as $t)
			{
				$booking = new Booking();
				$booking->course = $t['num'];
				$booking->day = $t['day'];
				$booking->time = $slot['time'];
				$booking->room = $t['room'];
				$booking->type = $t['type'];
				$booking->instructor = $t['instructor'];
				array_push($timeBookings, $booking);
			}
		}
		$this->timeslots[(string) $slot['start']] = $slot;
	}
	
	function getDaysOfWeek()
	{
		print_r($this->days);
		return $this->days;
	}
	
	function getDay($name)
	{
		if (isset($this->days[$name]))
			return $this->days[$name];
		else
			return null;
	}
	
	function getTimeslots()
	{
		return $this->timeslots;
	}
	
	function getTimeslot($slot)
	{
		if (isset($this->timeslots[$slot]))
			return $this->timeslots[$slot];
		else
			return null;
	}
	
	function getCourses()
	{
		return $this->courses;
	}
	
	function getCourse($course)
	{
		if (isset($this->courses[$course]))
			return $this->courses[$course];
		else
			return null;
	}
	
	 function query($day, $time, $course)
    {
        $result = array();
        $dayResult = array();
        $courseResult = array();
        $periodResult = array();
        if ($day !== "void")
            $dayResult = $this->queryDay($day);
        if ($time !== "void")
            $periodResult = $this->queryTime($time);
        if ($course !== "void")
            $courseResult = $this->queryCourse($course);
        array_push($result, $dayResult);
        array_push($result, $periodResult);
        array_push($result, $courseResult);
        return $result;
    }
}

class Booking
{
	public $course;
	public $day;
	public $time;
	public $room;
	public $type;
	public $instructor;
}
