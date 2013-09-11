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
 * @method \Magento\Core\Model\Layout\Update setIsTemporary() setIsTemporary(int $isTemporary)
 * @method \Magento\Core\Model\Layout\Update setHandle() setHandle(string $handle)
 * @method \Magento\Core\Model\Layout\Update setXml() setXml(string $xml)
 * @method \Magento\Core\Model\Layout\Update setStoreId() setStoreId(int $storeId)
 * @method \Magento\Core\Model\Layout\Update setThemeId() setThemeId(int $themeId)
 * @method \Magento\Core\Model\Layout\Update setUpdatedAt() setUpdatedAt(string $updateDateTime)
 * @method \Magento\Core\Model\Resource\Layout\Update\Collection getCollection()
 */
namespace Magento\Core\Model\Layout;

class Update extends \Magento\Core\Model\AbstractModel
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Layout\Update');
    }

    /**
     * Set current updated date
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        $this->setUpdatedAt($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
}
