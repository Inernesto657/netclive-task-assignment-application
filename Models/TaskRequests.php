<?php
namespace Models;
use Core\Model;

/**
 * This Class handles all Model functionalities made to the task_requests DB
 * Class TaskRequests
 * @package Models
 */
class TaskRequests extends Model{
    /**
     * Name of the database table
     * @var string $DBTABLE
     */
    public string $DBTABLE = "task_requests";
    
}
?>