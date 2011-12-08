<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging item model
 *
 * @method Enterprise_Staging_Model_Resource_Staging_Item _getResource()
 * @method Enterprise_Staging_Model_Resource_Staging_Item getResource()
 * @method int getStagingId()
 * @method Enterprise_Staging_Model_Staging_Item setStagingId(int $value)
 * @method string getCode()
 * @method Enterprise_Staging_Model_Staging_Item setCode(string $value)
 * @method int getSortOrder()
 * @method Enterprise_Staging_Model_Staging_Item setSortOrder(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Item extends Mage_Core_Model_Abstract
{
    /**
     * constructor
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Staging_Model_Resource_Staging_Item');
    }

    public function loadFromXmlStagingItem($xmlItem)
    {
        $this->setData('code', (string) $xmlItem->getName());
        $name = Mage::helper('Enterprise_Staging_Helper_Data')->__((string) $xmlItem->label);
        $this->setData('name', $name);
        return $this;
    }
}
