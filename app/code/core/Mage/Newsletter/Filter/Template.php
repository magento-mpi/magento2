<?php
/**
 * Template constructions filter
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Newsletter_Filter_Template implements Zend_Filter_Interface
{
    const CONSTRUCTION_PATTERN = '/{(in[sc][a-z]{3,4})(.*?)}/si';
        
    protected $_templateVars = array();
    protected $_includeProcessor = null;
    protected $_allowedDirectives = array('insvar', 'include');
    
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
        return $this->_includeProcessor;
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
                       
                        if(isset($this->_templateVars[trim($constructions[2][$index])])) {
                            $replacedValue = $this->_templateVars[trim($constructions[2][$index])];
                        }
                        break;
                    
                    case "include":
                        $includeParameters = $this->_getIncludeParameters($constructions[2][$index]);
                        if(!isset($includeParameters['template']) or !$this->getIncludeProcessor()) {
                            $replacedValue = '{' . __('error in include processing') . '}';
                        } else { 
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
        $tokenizer = new Mage_Newsletter_Filter_Template_ParameterTokenizer();
        $tokenizer->setString($value);
        
        return $tokenizer->tokenize();
    }
}