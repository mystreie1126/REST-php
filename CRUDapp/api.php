<!-- 
Code reference: 
1. https://www.codeofaninja.com/2017/02/create-simple-rest-api-in-php.html
2. https://www.leaseweb.com/labs/2015/10/creating-a-simple-rest-api-in-php/
3. https://www.youtube.com/watch?v=_aiXLAp3OwE&t=2498s
4. https://www.youtube.com/watch?v=DHUxnUX7Y2Y&t=347s

-->


<?php

// start with my Database connection
require_once "database.php";

class Api {
    private $db; // database connection
    public $result = array(); // result array

    /*
     * Constructor
     */
    public function __construct($db) {
        $this->db = $db;
    }

   /* process the data...*/
    public function process($aData) {
        //  validate request data
        if (!$this->validate($aData)) {
            return $this->result;
        }
        $action = $aData['action'];

        return $this->$action((array)$aData['data']); // return result: create/read/update/delete
    }

    private function create($data) {
        unset($data['id']); // remove unused parameter
        // prepare PDO statement for insert new value  ???
        $item = $this->db->prepare("INSERT INTO `contacts` (`name`, `email`, `phone`)
            VALUES (:fullname, :email, :phone)");
        try {
            $item->execute($data); 
            $data['id'] = $this->db->lastInsertId(); 
            $this->result = array( 
                'success' => 1,
                'action' => 'Create',
                'data' => $data
            );
        } catch (PDOException $e) { 
            $this->result = array(
                'success' => 0,
                'error' => 'An error during creation new entry'
            );
        }
        return $this->result; 
    }

    private function read($data) {
        //prepare PDO statement for reading an item / items
        $query = "SELECT * FROM `contacts` WHERE `id` = :id";
        $binds['id'] = $data['id']; // binds array
        $item = $this->db->prepare($query);
        try {
            $item->execute($binds); // execute database query
            $data = $item->fetch(); // get row
            $this->result = array( // fill the answer
                'success' => 1,
                'action' => 'Read',
                'data' => $data
            );
        } catch (PDOException $e) { // process errors
            $this->result = array(
                'success' => 0,
                'error' => 'An error during reading'
            );
        }
        return $this->result; 
    }

    private function update($data) {
        # prepare PDO statement
        $item = $this->db->prepare("UPDATE `contacts` SET `name` = :fullname, `email` = :email, `phone` = :phone WHERE `id` = :id");
        try {
            $item->execute($data); // execute database query ???
            $this->result = array( // fill the answer
                'success' => 1,
                'action' => 'Update',
                'data' => $data
            );
        } catch (PDOException $e) { // process errors
            $this->result = array(
                'success' => 0,
                'error' => 'An error during update entry'
            );
        }
        return $this->result; // return result
    }

    private function delete($data) {
        # prepare PDO statement
        $item = $this->db->prepare("DELETE FROM `contacts` WHERE `id` = :id");
        try {
            $item->execute($data); // execute database query
            $this->result = array( // fill the answer
                'success' => 1,
                'action' => 'Delete',
                'data' => $data
            );
        } catch (PDOException $e) { // process errors
            $this->result = array(
                'success' => 0,
                'error' => 'An error during delete entry'
            );
        }
        return $this->result; // return result
    }

    private function load($data) {
        // prepare PDO statement
        $query = "SELECT * FROM `contacts`";
        $item = $this->db->prepare($query);
        try {
            $item->execute(); // execute database query
            $data = $item->fetchAll(); // get all rows
            $this->result = array( // fill the answer
                'success' => 1,
                'action' => 'Load',
                'data' => $data
            );
        } catch (PDOException $e) { // process errors
            $this->result = array(
                'success' => 0,
                'error' => 'An error during loading'
            );
        }
        return $this->result; // return result
    }

    private function validate($aData) {
        $valid = true; // init default validation answer
        if (!is_array($aData)) {
            $valid = false;
            $this->result = array(
                'success' => 0,
                'error' => 'Not valid request: must be JSON'
            );
        } else if (!isset($aData['action']) || !isset($aData['data'])) {
            $valid = false;
            $this->result = array(
                'success' => 0,
                'error' => 'Not valid request: some of required fields are missed'
            );
        } else if (!in_array($aData['action'], array('create', 'read', 'update', 'delete', 'load'))) {
            $valid = false;
            $this->result = array(
                'success' => 0,
                'error' => 'Not valid request: illegal action'
            );
        }

        return $valid;
    }
}

/* key varaibles */

$request = file_get_contents('php://input'); // read input stream to catch json with data
$data = (array) json_decode($request); // decode data to array
$api = new Api($db); // create an instance of API for process call
$result = $api->process($data); // process API call

echo json_encode($result); // --> return result in json format