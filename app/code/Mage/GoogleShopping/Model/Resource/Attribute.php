<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Attributes resource model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Resource_Attribute extends Magento_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('googleshopping_attributes', 'id');
    }
}
