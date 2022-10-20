<?php
namespace Models;
use Core\Model;

/**
 * This Class handles all Model functionalities made to the users DB
 * Class Users
 * @package Models
 */
class Users extends Model{
    /**
     * Name of the database table
     * @var string $DBTABLE
     */
    public string $DBTABLE = "users";
    
}
?>