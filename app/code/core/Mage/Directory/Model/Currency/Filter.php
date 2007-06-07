<?php
/**
 * Currency filter
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Currency_Filter extends Varien_Filter_Sprintf 
{
    protected $_rate;
    
    public function __construct($format, $decimals=null, $decPoint='.', $thousandsSep=',', $rate=1)
    {
        parent::__construct($format, $decimals, $decPoint, $thousandsSep);
        $this->_rate = $rate;
    }
    
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }
    
    public function filter($value)
    {
        $value = $this->_rate*$value;
        return parent::filter($value);
    }
}
