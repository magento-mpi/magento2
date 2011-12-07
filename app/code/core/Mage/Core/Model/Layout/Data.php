<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enter description here ...
 *
 * @method Mage_Core_Model_Resource_Layout _getResource()
 * @method Mage_Core_Model_Resource_Layout getResource()
 * @method string getHandle()
 * @method Mage_Core_Model_Layout_Data setHandle(string $value)
 * @method string getXml()
 * @method Mage_Core_Model_Layout_Data setXml(string $value)
 * @method int getSortOrder()
 * @method Mage_Core_Model_Layout_Data setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Data extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Layout');
    }
}
