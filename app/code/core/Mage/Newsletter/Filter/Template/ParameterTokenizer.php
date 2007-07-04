<?php
/**
 * Template constructions parameters tokenizer
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Newsletter_Filter_Template_ParameterTokenizer
{
    /**
     * Current index in string
     * @var int
     */
    protected $_currentIndex;
    
    /**
     * String for tokenize
     */
    protected $_string;
    
    /**
     * Move current index to next char. 
     *
     * If index out of bounds returns false
     *
     * @return boolean
     */
    public function next() 
    {
        if($this->_currentIndex + 1 >= strlen($this->_string)) {
            return false;
        }
        
        $this->_currentIndex++;
        return true; 
    }
    
    /**
     * Move current index to previus char. 
     *
     * If index out of bounds returns false
     *
     * @return boolean
     */
    public function prev() 
    {
        if($this->_currentIndex - 1 < 0) {
            return false;
        }
        
        $this->_currentIndex--;
        return true; 
    }
    
    /**
     * Return current char 
     *
     * @return string
     */
    public function char()
    {
        return $this->_string{$this->_currentIndex};
    }
    
    
    /**
     * Set string for tokenize
     */
    public function setString($value)
    {
        $this->_string = $value;
        $this->reset();
    }
    
    /**
     * Move char index to begin of string
     */
    public function reset() {
        $this->_currentIndex = 0;
    }
    
    /**
     * Tokenize string and return getted parameters
     *
     * @return array
     */
    public function tokenize() 
    {
        $parameters = array();
        $parameterName = '';
        while($this->next()) {
            if($this->isWhiteSpace()) {
                continue;
            } else if($this->char()!='=') {
                $parameterName .= $this->char();
            } else {
                $parameters[$parameterName] = $this->getValue();
                $parameterName = '';
            }
            
        }       
        return $parameters;
    }
    
    /**
     * Get string value in parameters througth tokenize
     * 
     * @return string
     */
    public function getValue() {
        $this->next();
        $value = '';
        if($this->isWhiteSpace()) { 
            return $value;
        }
        $qouteStart = $this->char() == "'" || $this->char() == '"';
        
        
        if($qouteStart) {
           $breakSymbol = $this->char();
        } else { 
           $breakSymbol = false;
           $value .= $this->char();
        }
          
        while ($this->next()) {
            if (!$breakSymbol && $this->isWhiteSpace()) {
                break;
            } else if ($breakSymbol && $this->char() == $breakSymbol) {
                break;
            } else if ($this->char() == '\\') {
                $this->next();
                $value .= $this->char();
            } else {
                $value .= $this->char();
            }                        
        }
        
        return $value;
    }
    
    /**
     * Return true if current char is white-space
     *
     * @return boolean
     */
    public function isWhiteSpace() {
        return trim($this->char()) != $this->char();
    }
}