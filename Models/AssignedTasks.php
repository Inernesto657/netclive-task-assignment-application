<?php
namespace Models;
use Core\Model;

/**
 * This Class handles all Model functionalities made to the assigned_tasks DB
 * Class AssignedTasks
 * @package Models
 */
class AssignedTasks extends Model{
    /**
     * Name of the database table
     * @var string $DBTABLE
     */
    public string $DBTABLE = "assigned_tasks";
    
}
?>