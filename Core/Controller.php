<?php

namespace Core;
use Core\View;

/**
 * This Class serves as the parent class to all controllers
 * Class Controller
 * @package Core
 */
abstract class Controller {

    /**
     * instantiates the View class in order to display the intended veiw
     * @return object
     */
    public function view($view, $data = []) {
        return new View($view, $data);
    }

}
?>