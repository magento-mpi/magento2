<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Template constructions tokenizer
 *
 * @category   Magento
 * @package    Magento_Filter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Filter\Template\Tokenizer;

abstract class AbstractTokenizer
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
    public function reset() 
    {
        $this->_currentIndex = 0;
    }
    
    /**
     * Return true if current char is white-space
     *
     * @return boolean
     */
    public function isWhiteSpace() {
        return trim($this->char()) != $this->char();
    }
    
    abstract public function tokenize();
    
}
