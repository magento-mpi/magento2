<?php
/**
 * Entity attribute option resource model
 *
 * @package     Mage
 * @subpackage  Eav
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute_Option extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() 
    {
        $this->_init('eav/attribute_option', 'option_id');
    }
}
