<?php

namespace Mozaiku;

/*

  Mozaiku - Plain PHP Template Inheritance Class
  ------------------------
  Version: 1.0
  Author: Lorenzo Sanzari - https://it.linkedin.com/in/lorenzosanzari
  Released under the MIT License (file license.txt)

 */

class Mozaiku {

    public $debug_mode = false;
    public $strict_mode = false; //throw exception in case of non optimal view definition
    protected $sections = []; //executed sections list
    protected $prev_sections = []; //sections of previous views, not yet used for override
    protected $sections_file = []; //associative array of section => file
    protected $stacks = []; //stacks for assets and others contents
    protected $current_stack = null; //the stack on which we are doing push  
    protected $sections_names_stack = []; //sections encountered in this view, also nested
    protected $parent_view = null; // any parent view (extendsView)
    protected $current_file = null; //current view file
    protected $view_data = []; //view variables
    protected $current_main = ''; //extra section captured content
    protected $page_level = 0; //page level in the hierarchy (first starting page = 1)
    protected $sections_op_cl_count = 0; //sections open/close balance

    // API METHODS /////////////////////////////////////////////////////////////

    public function __construct() {
        //...
    }

    // -------------------------------------------------------------------------
    /**
     * Return (or print) the rendered view content.
     * 
     * @param string $file
     * @param array $data
     * @param bool $return
     * @return string
     */
    public function render($file, $data = [], $return = false) {
        $this->clear();
        $this->view_data = $data;
        unset($data); //free memory
        $this->processView($file);
        if ($return)
            return $this->current_main;
        echo $this->current_main; //only in the final view
    }

    // -------------------------------------------------------------------------

    /**
     * Set the parent view path.
     * 
     * @param string $file
     */
    public function extendsView($file) {
        if (file_exists($file)) {
            $this->parent_view = $file;
        } else {
            $this->err("The parent view '" . $file . "' extended by '" . $this->current_file . "' does not exists!");
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Include another view in the current one.
     * 
     * @param string $file
     * @param array $data
     */
    public function includeView($file, $data = []) {
        if (file_exists($file)) {
            extract($this->view_data); //view variables
            extract($data); //additional variables
            include $file;
        } else {
            $this->err("The view '" . $file . "' included by '" . $this->current_file . "' does not exists!");
        }
    }

    // -------------------------------------------------------------------------

    /**
     * "Mount" another's view output in your current view.
     * E.g.: in your current view
     * 
     *  echo $this->view("another_view.php", $additional_data);
     * 
     * @param string $file
     * @param array $data
     * @param bool $return
     * @return string
     */
    public function view($file, $data = [], $return = false) {
        $view = new Mozaiku();
        $data = array_merge($this->view_data, $data);
        return $view->render($file, $data, $return);
    }

    // -------------------------------------------------------------------------

    /**
     * Insert the parent section content in your current section.
     */
    public function parentContent() {
        echo '{{__PARENT__CONTENT__}}'; //insert placeholder to replace 
    }

    // -------------------------------------------------------------------------

    /**
     * Show an undefined content section.
     * 
     * @param string $name
     */
    public function showsection($name) {
        $this->section($name);
        $this->endsection();
    }

    // -------------------------------------------------------------------------

    /**
     * Open a section content.
     * 
     * @param string $name
     */
    public function section($name = null) {

        if (!($this->strict_mode == true && isset($this->sections_file[$name]) && $this->sections_file[$name] == $this->current_file)) {
            $this->sections_op_cl_count++;
            $this->sections_names_stack[] = $name;
            $this->sections_file[end($this->sections_names_stack)] = $this->current_file;
            ob_start();
        } else {
            $this->err("Duplicated '" . $name . "' section in " . $this->sections_file[$name]);
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Close the current section.
     */
    public function endsection() {
        $this->sections_op_cl_count--;
        $this->sections[end($this->sections_names_stack)] = trim(ob_get_clean()) . "\n";
        $this->processSection();
    }

    /**
     * Render a stack content.
     * 
     * @param string $name
     */
    public function stack($stack) {
        if (isset($this->stacks[$stack])) {
            echo "\n" . implode("\n", $this->stacks[$stack]) . "\n";
        }
    }

    /**
     * Start content push.
     */
    public function push($stack) {
        $this->current_stack = $stack;
        ob_start();
    }

    /**
     * End content push.
     */
    public function endpush() {
        $content = ob_get_clean();
        $this->stacks[$this->current_stack][] = $content;
        $this->current_stack = null;
    }

    ////////////////////////////////////////////////////////////////////////////

    protected function isSingle() { //the view does not extends another view
        return ($this->page_level == 1 && empty($this->parent_view));
    }

    protected function isMiddle() { //it is an intermediate view
        return ($this->page_level > 1 && !empty($this->parent_view));
    }

    protected function isFinal() {//it is the final view (layout)
        return ($this->page_level > 1 && empty($this->parent_view));
    }

    /**
     * Current section nesting level.
     * 
     * @return integer
     */
    protected function currentSectionLevel() {
        return count($this->sections_names_stack);
    }

    /**
     * Process the view template.
     * 
     * @param string $file
     */
    protected function processView($file) {
        extract($this->view_data); //view variables
        $this->page_level++;
        $this->current_file = $file;
        $this->sections = [];

        ob_start();

        if (file_exists($file)) {
            include $file;
        } else {
            $this->err("The view '" . $file . "' does not exists!");
        }

        $this->current_main = ob_get_clean();

        //////////////////////////////
        if ($this->debug_mode == true)
            $this->printDebug();

        /////////////////////////////
        //Verify open/closed section balance
        if ($this->sections_op_cl_count > 0) {
            $this->err("N° " . $this->sections_op_cl_count . " not closed sections in '" . $file . "'");
        }

        if (!($this->isSingle() || $this->isFinal())) {
            //Check extra section content (only in strict mode)
            if ($this->strict_mode == true && !empty(trim($this->current_main)))
                $this->err("Extra section content not allowed in '" . $file . "'");
            //Continua la gerarchia...
            $this->prev_sections = array_merge($this->sections, $this->prev_sections); //section override forwarding to the layout
            $this->sections = [];
            $parent = $this->parent_view;
            $this->parent_view = null; //reset for the next view
            $this->processView($parent);
        }
    } //end process view
    
    
    /**
     * Process the current section.
     */
    protected function processSection() {
        //Check if there is override content available for this section
        if (isset($this->prev_sections[end($this->sections_names_stack)])) {
            //Replace the parent content placeholder with parent section content
            $this->sections[end($this->sections_names_stack)] = str_replace('{{__PARENT__CONTENT__}}', $this->sections[end($this->sections_names_stack)], $this->prev_sections[end($this->sections_names_stack)]);
            unset($this->prev_sections[end($this->sections_names_stack)]); //destroy used override content
        }

        /**
         * If we are in a single view or in the final theme,
         * every block of the page is immediately printed
         * (accumulated in the $main buffer)
         */
        if ($this->isSingle() || $this->isFinal() || $this->currentSectionLevel() > 1) {
            echo $this->sections[end($this->sections_names_stack)]; //l'eco finisce nel contenuto di $main
            unset($this->sections[end($this->sections_names_stack)]);
        }

        array_pop($this->sections_names_stack); //section processed
    }

    // Helpers /////////////////////////////////////////////////////////////////

    /**
     * Print error exception with custom message.
     * 
     * @param string $msg
     * @throws Exception
     */
    protected function err($msg) {
        throw new Exception('Mozaiku error: ' . $msg);
    }

    /**
     * Clear all class variables values (reset).
     */
    protected function clear() {
        $this->sections = [];
        $this->prev_sections = [];
        $this->sections_file = [];
        $this->stacks = [];
        $this->current_stack = null;
        $this->sections_names_stack = [];
        $this->parent_view = null;
        $this->current_file = null;
        $this->view_data = [];
        $this->current_main = '';
        $this->page_level = 0;
        $this->sections_op_cl_count = 0;
    }

    /**
     * Some debug information (if $this->debug_mode = true)
     */
    protected function printDebug() {
        echo "<hr><strong>FILE: " . $this->current_file . "</strong>";
        echo "<br>Prev sections: " . print_r($this->prev_sections, true);
        echo "<br>Sections: " . print_r($this->sections, true);
        echo "<br>Extra section: " . $this->current_main;
        echo "<hr>";
    }

} //end class

