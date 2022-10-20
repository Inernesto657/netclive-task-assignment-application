<?php
namespace Models;
use Core\Model;

/**
 * This Class handles all Model functionalities made to the tasks DB
 * Class Tasks
 * @package Models
 */
class Tasks extends Model{
    /**
     * Name of the database table
     * @var string $DBTABLE
     */
    public string $DBTABLE = "tasks";
    
}
?>