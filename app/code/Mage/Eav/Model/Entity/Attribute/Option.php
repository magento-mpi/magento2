<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Emtity attribute option model
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option getResource()
 * @method int getAttributeId()
 * @method Mage_Eav_Model_Entity_Attribute_Option setAttributeId(int $value)
 * @method int getSortOrder()
 * @method Mage_Eav_Model_Entity_Attribute_Option setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Option extends Magento_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Mage_Eav_Model_Resource_Entity_Attribute_Option');
    }
}
