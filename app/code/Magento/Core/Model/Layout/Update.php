<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout;

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
class Update extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

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
        $this->setUpdatedAt($this->_dateTime->formatDate(time()));
        return parent::_beforeSave();
    }
}
