<?php
/**
* MSG Sales Competition - Database actions
*
* @author Ted Coyle <ted.coyle@nutrigraphix.com>
*/

class SellerDB extends mysqli {

    private static $instance = null;

    private $user = "";
    private $pass = "";
    private $dbName = "";
    private $dbHost = "";

    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    private function __construct() {
        parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
        parent::set_charset('utf-8');
    }

    public function get_seller_info($uname) {
        $uname = $this->real_escape_string($uname);
        return $this->query("SELECT * FROM msg_users WHERE uname='" . $uname . "' LIMIT 1");
    }       

    public function save_meeting($uid, $uname, $date, $mdate, $agency, $a_attendees, $client, $c_attendees, $michael_britta, $points) {
        $uid = $this->real_escape_string($uid);
        $uname = $this->real_escape_string($uname);
        $date = $this->real_escape_string($date);
        $mdate = $this->real_escape_string($mdate);
        $agency = $this->real_escape_string($agency);
        $a_attendees = $this->real_escape_string($a_attendees);
        $client = $this->real_escape_string($client);
        $c_attendees = $this->real_escape_string($c_attendees);
        $michael_britta = $this->real_escape_string($michael_britta);
        $points = $this->real_escape_string($points);
        $this->query("INSERT INTO msg_records (uid,uname,date,mdate,agency,a_attendees,client,c_attendees,michael_britta,points) 
        			  VALUES ('".$uid."', '".$uname."', '".$date."', '".$mdate."', '".$agency."', '".$a_attendees."', '".$client."', '".$c_attendees."', '".$michael_britta."', '".$points . "')");
    }

    public function delete_meeting($rec_num) {
        $rec_num = $this->real_escape_string($rec_num);
        $this->query("DELETE FROM msg_records WHERE rec_num = '".$rec_num."'");
    }

    public function add_total_points($uid,$points) {
        $uid = $this->real_escape_string($uid);
        $points = $this->real_escape_string($points);
        $this->query("UPDATE msg_users SET total_points = total_points + ".$points." WHERE uid = ".$uid);
    }

    public function subtract_total_points($uid,$points) {
        $uid = $this->real_escape_string($uid);
        $points = $this->real_escape_string($points);
        $this->query("UPDATE msg_users SET total_points = total_points - ".$points." WHERE uid = ".$uid);
    }  
    
    public function get_meetings_by_seller($uid,$limit) {
        $uid = $this->real_escape_string($uid);
        $limit = $this->real_escape_string($limit);
        return $this->query("SELECT * FROM msg_records WHERE uid='" . $uid . "' ORDER BY rec_num DESC LIMIT ".$limit);
    }

    public function get_summary_by_group($group) {
        $group = $this->real_escape_string($group);
        if (!$query = $this->query("SELECT * FROM msg_users WHERE `group`='".$group."' ORDER BY `department` ASC,`total_points` DESC") ) {
            return die( printf($this->error) );
        } else {
            return $query;
        }
         
    }

    public function get_points_by_group( $group ) {
        $group = $this->real_escape_string($group);
        
        if ($query = $this->query("SELECT sum(`total_points`) FROM `msg_users` WHERE `group` = '".$group."'") ) {
            $row = $query->fetch_row();
            return $row[0];
            $query->close();
        }

    }

    public function get_top_ten() {
        return $this->query("SELECT * FROM msg_users ORDER BY total_points DESC LIMIT 10");
    }

    public function get_all_points() {
        $departments = array();
        $pointsByDept = array();
        $pointsByDept[0] = array('department','points');

        $query = $this->query("SELECT DISTINCT(`department`) AS `department` FROM `msg_users`");
            while ($row = mysqli_fetch_array($query)) {
                $departments[] = $row['department'];
            }

        foreach ($departments as $department) {
            $debtdb = $this->real_escape_string($department);
            $query = $this->query("SELECT sum(`total_points`) AS `points` FROM `msg_users` WHERE `department` = '".$debtdb."'");
            $row = $query->fetch_row();
            $score = array($department, $row[0]);
            $pointsByDept[] = $score;
            $query->close();
        }
        //delete header row
        $remove = array_shift($pointsByDept);
        return $pointsByDept;
    }

    public function download_brochure($fname, $lname, $title, $company, $landingPage, $email) {
        //authenticated user downloads brochure
        $fname = $this->real_escape_string($_POST['fname']);
        $lname = $this->real_escape_string($_POST['lname']);
        $title = $this->real_escape_string($_POST['title']);
        $company = $this->real_escape_string($_POST['company']);
        $landingPage = $this->real_escape_string($landingPage);
        $email = $this->real_escape_string($_POST['email']);

        $this->query("INSERT into em_downloads (fname, lname, title, company, landing_page, email) 
                        VALUES ('".$fname."', '".$lname."', '".$title."', '".$company."', '".$landingPage."', '".$email."')");
    }


}

?>