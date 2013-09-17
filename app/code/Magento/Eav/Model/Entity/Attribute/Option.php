<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Emtity attribute option model
 *
 * @method Magento_Eav_Model_Resource_Entity_Attribute_Option _getResource()
 * @method Magento_Eav_Model_Resource_Entity_Attribute_Option getResource()
 * @method int getAttributeId()
 * @method Magento_Eav_Model_Entity_Attribute_Option setAttributeId(int $value)
 * @method int getSortOrder()
 * @method Magento_Eav_Model_Entity_Attribute_Option setSortOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute_Option extends Magento_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Entity_Attribute_Option');
    }
}
