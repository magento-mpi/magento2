<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Template constructions parameters tokenizer
 *
 * @category   Varien
 * @package    Varien_Filter
 * @author      Magento Core Team <core@magentocommerce.com>
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