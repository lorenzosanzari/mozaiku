<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files

use Mozaiku\Mozaiku;

/**
 * Controller example class
 */
class controller{
    protected $view;
    
    public function __construct() {
        $this->view = new Mozaiku();
        $this->view->debug = false; //debug mode
        $this->view->strict_mode = false; //strict mode (see readme.md)
        
    }
        
    public function index(){
        $this->view->render('views/page.php');
    }
}

//-------------------------

//Usage
$c = new controller();
$c->index();


//-------------------------





