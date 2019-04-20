<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcalendar extends MY_Model {

    protected $table = 'tblevents';
    public function __construct()
    {
        parent::__construct();
        // $this->db = $this->load->database('calendar', TRUE);
    }
    public function getEvents()
    {
        $sql = "SELECT title,description,color,start,end FROM ".$this->table." WHERE ".$this->table.".start BETWEEN ? AND ? ORDER BY ".$this->table.".start ASC";
        return $this->db->query($sql, array($_GET['start'], $_GET['end']))->result();
    }
    public function addEvent()
    {
        $sql = "INSERT INTO ".$this->table." (title,".$this->table.".start,".$this->table.".end,description, color) VALUES (?,?,?,?,?)";
        $this->db->query($sql, array($_POST['title'], $_POST['start'],$_POST['end'], $_POST['description'], $_POST['color']));
        return ($this->db->affected_rows()!=1)?false:true;
    }
    public function updateEvent()
    {
        $sql = "UPDATE ".$this->table." SET title = ?, description = ?, color = ?,start=?,end=? WHERE id = ?";
        $this->db->query($sql, array($_POST['title'],$_POST['description'], $_POST['color'],$_POST['start'],$_POST['end'], $_POST['id']));
            return ($this->db->affected_rows()!=1)?false:true;
    }
    public function deleteEvent()
    {
        $sql = "DELETE FROM ".$this->table." WHERE id = ?";
        $this->db->query($sql, array($_GET['id']));
        return ($this->db->affected_rows()!=1)?false:true;
    }
    public function dragUpdateEvent()
    {
        //$date=date('Y-m-d h:i:s',strtotime($_POST['date']));
        $sql = "UPDATE ".$this->table." SET  ".$this->table.".start = ? ,".$this->table.".end = ?  WHERE id = ?";
        $this->db->query($sql, array($_POST['start'],$_POST['end'], $_POST['id']));
        return ($this->db->affected_rows()!=1)?false:true;
    }

}