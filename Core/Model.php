<?php
namespace Core;
use Core\DB;

abstract class Model extends DB{

    public function __construct() {
        $this->connection();
    }
}
?>