<?php
/**
 * Emtity attribute option model
 *
 * @package     Mage
 * @subpackage  Eav
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Eav_Model_Entity_Attribute_Option extends Mage_Core_Model_Abstract 
{
    public function _construct() 
    {
        $this->_init('eav/entity_attribute_option');
    }
}
