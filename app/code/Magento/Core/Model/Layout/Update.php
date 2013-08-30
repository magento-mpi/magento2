<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout Update model class
 *
 * @method int getIsTemporary() getIsTemporary()
 * @method int getLayoutLinkId() getLayoutLinkId()
 * @method string getUpdatedAt() getUpdatedAt()
 * @method string getXml() getXml()
 * @method Magento_Core_Model_Layout_Update setIsTemporary() setIsTemporary(int $isTemporary)
 * @method Magento_Core_Model_Layout_Update setHandle() setHandle(string $handle)
 * @method Magento_Core_Model_Layout_Update setXml() setXml(string $xml)
 * @method Magento_Core_Model_Layout_Update setStoreId() setStoreId(int $storeId)
 * @method Magento_Core_Model_Layout_Update setThemeId() setThemeId(int $themeId)
 * @method Magento_Core_Model_Layout_Update setUpdatedAt() setUpdatedAt(string $updateDateTime)
 * @method Magento_Core_Model_Resource_Layout_Update_Collection getCollection()
 */
class Magento_Core_Model_Layout_Update extends Magento_Core_Model_Abstract
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Layout_Update');
    }

    /**
     * Set current updated date
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setUpdatedAt($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
}
