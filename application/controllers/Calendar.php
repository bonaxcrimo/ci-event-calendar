<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->load->model('Mcalendar');
    }
    /**
     * tampilan awal calendar
     * @AclName index calendar
     */
    public function index()
    {
        $this->load->view('calendar/index');
    }
    /**
     * tampilan add event
     * @AclName add event
     */
    public function addEvent()
    {
        $result=$this->Mcalendar->addEvent();
        echo $result;
    }
    /**
     * tampilan update event
     * @AclName update event
     */
    public function updateEvent()
    {
        $result=$this->Mcalendar->updateEvent();
        echo $result;
    }
    /**
     * tampilan delete event
     * @AclName delete event
     */
    public function deleteEvent()
    {
        $result=$this->Mcalendar->deleteEvent();
        echo $result;
    }
    /**
     * tampilan dragUpdateEvent
     * @AclName dragUpdateEvent
     */
    public function dragUpdateEvent()
    {
        $result=$this->Mcalendar->dragUpdateEvent();
        echo $result;
    }

}
