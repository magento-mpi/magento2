<?php
/**
 * Entity int attribute type
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity_Attribute_Type_Int extends Mage_Core_Model_Entity_Attribute_Type_Abstract
{
    public function __construct() 
    {
        $this->_code = 'int';
    }
}
