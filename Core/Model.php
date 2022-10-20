<?php
namespace Core;
use Core\DB;

/**
 * This Class serves as the parent class to all models
 * Class Model
 * @package Core
 */
abstract class Model extends DB{

    /**
     * makes a connection to the DB once
     * a model is called
     */
    public function __construct() {
        $this->connection();
    }
}
?>