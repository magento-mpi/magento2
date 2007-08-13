<?php
/**
 * Template constructions parameters tokenizer
 *
 * @package     Varien
 * @subpackage  Filter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Varien_Filter_Template_Tokenizer_Parameter extends Varien_Filter_Template_Tokenizer_Abstract
{
       
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
        $quoteStart = $this->char() == "'" || $this->char() == '"';
        
        
        if($quoteStart) {
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
    
}