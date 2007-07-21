<?php
/**
 * Template constructions filter
 *
 * @package     Varien
 * @subpackage  Template
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Varien_Filter_Template implements Zend_Filter_Interface
{
    /** 
     * Cunstruction regular expression
     */
    const CONSTRUCTION_PATTERN = '/{([a-z]{0,10})(.*?)}/si';
    
    /**
     * Assigned template variables
     *
     * @var array
     */
    protected $_templateVars = array();
    
    /**
     * Include processor
     *
     * @var array|string|null
     */
    protected $_includeProcessor = null;
    
    /**
     * Allowed template directives
     * @var array
     */
    protected $_allowedDirectives = array('insvar', 'include');
    
    /**
     * Sets template variables that's can be called througth {insvar ...} statement
     * 
     * @param array $variables
     */
    public function setVariables(array $variables) 
    {
        foreach($variables as $name=>$value) {
            $this->_templateVars[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Sets the proccessor of includes.
     *
     * @param array $callback it must return string
     */
    public function setIncludeProcessor(array $callback) 
    {
        $this->_includeProcessor = $callback;
        return $this;
    }
    
    /**
     * Sets the proccessor of includes.
     *
     * @return array|null
     */
    public function getIncludeProcessor() 
    {
        return is_callable($this->_includeProcessor) ? $this->_includeProcessor : null;
    }
    
    /**
     * Filter the string as template.
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        
        if(preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions)) {
            foreach($constructions[1] as $index=>$directive) {
                $replacedValue = '';
                if(!in_array($directive, $this->_allowedDirectives)) {
                    continue;
                }
                switch($directive) {
                    case "insvar":
                        // Processing of {insvar ...} statement
                        $replacedValue = $this->_getVariable($constructions[2][$index], $constructions[0][$index]);
                        break;
                    
                    case "include":
                        // Processing of {include template=... [...]} statement
                        $includeParameters = $this->_getIncludeParameters($constructions[2][$index]);
                        if(!isset($includeParameters['template']) or !$this->getIncludeProcessor()) {
                            // Not specified template or not seted include processor
                            $replacedValue = '{' . __('Error in include processing') . '}';
                        } else { 
                            // Including of template
                            $templateCode = $includeParameters['template'];
                            unset($includeParameters['template']);
                            $replacedValue = call_user_func_array($this->getIncludeProcessor(), 
                                                                  array($templateCode,$includeParameters));
                        }
                        break;
                }
                
                $value = str_replace($constructions[0][$index], $replacedValue, $value);
            }
        }
        
        return $value;
    }
      
    
    /**
     * Return associative array of include construction.
     *
     * @param string $value raw parameters
     * @return array
     */
    protected function _getIncludeParameters($value) 
    {
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);
        
        return $tokenizer->tokenize();
    }
    
     /**
     * Return variable value for insvar construction
     *
     * @param string $value raw parameters
     * @param string $default default value
     * @return string
     */
    protected function _getVariable($value, $default='{no_value_defined}') 
    {
        Varien_Profiler::start("email_template_proccessing_variables");
        $tokenizer = new Varien_Filter_Template_Tokenizer_Variable();
        $tokenizer->setString($value);
        $stackVars = $tokenizer->tokenize();
        $result = $default;
        $last = 0;
        for($i = 0; $i < count($stackVars); $i ++) {
            if ($i == 0 && isset($this->_templateVars[$stackVars[$i]['name']])) {
                // Getting of template value
                $stackVars[$i]['variable'] =& $this->_templateVars[$stackVars[$i]['name']];
            } else if (isset($stackVars[$i-1]['variable']) 
                       && $stackVars[$i-1]['variable'] instanceof Varien_Object) {
                // If object calling methods or getting properties
                if($stackVars[$i]['type'] == 'property') {
                    $caller = "get" . uc_words($stackVars[$i]['name'], '');
                    if(is_callable(array($stackVars[$i-1]['variable'], $caller))) {
                        // If specified getter for this property
                        $stackVars[$i]['variable'] = $stackVars[$i-1]['variable']->$caller();
                    } else {
                        $stackVars[$i]['variable'] = $stackVars[$i-1]['variable']
                                                        ->getData($stackVars[$i]['name']);
                    }
                } else if ($stackVars[$i]['type'] == 'method') {
                    // Calling of object method
                    if (is_callable(array($stackVars[$i-1]['variable'], $stackVars[$i]['name'])) || substr($stackVars[$i]['name'],0,3) == 'get') {
                        $stackVars[$i]['variable'] = call_user_func_array(array($stackVars[$i-1]['variable'],
                                                                                $stackVars[$i]['name']),
                                                                          $stackVars[$i]['args']);
                    } 
                }
                $last = $i;
            }
        }
        
        if(isset($stackVars[$last]['variable'])) {
            // If value for construction exists set it
            $result = (string) $stackVars[$last]['variable'];
        }
        Varien_Profiler::stop("email_template_proccessing_variables");
        return $result;
    }
}
