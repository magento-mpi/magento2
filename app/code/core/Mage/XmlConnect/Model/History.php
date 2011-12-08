<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xmlconnect history model
 *
 * @method Mage_XmlConnect_Model_Resource_History _getResource()
 * @method Mage_XmlConnect_Model_Resource_History getResource()
 * @method int getApplicationId()
 * @method Mage_XmlConnect_Model_History setApplicationId(int $value)
 * @method string getCreatedAt()
 * @method Mage_XmlConnect_Model_History setCreatedAt(string $value)
 * @method int getStoreId()
 * @method Mage_XmlConnect_Model_History setStoreId(int $value)
 * @method string getParams()
 * @method Mage_XmlConnect_Model_History setParams(string $value)
 * @method string getTitle()
 * @method Mage_XmlConnect_Model_History setTitle(string $value)
 * @method string getActivationKey()
 * @method Mage_XmlConnect_Model_History setActivationKey(string $value)
 * @method string getCode()
 * @method Mage_XmlConnect_Model_History setCode(string $value)
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_History extends Mage_Core_Model_Abstract
{
    /**
     * Initialize application
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('Mage_XmlConnect_Model_Resource_History');
    }

    /**
     * Get array of existing images
     *
     * @param int $id Application instance Id
     * @return array
     */
    public function getLastParams($id)
    {
        return $this->_getResource()->getLastParams($id);
    }
}
