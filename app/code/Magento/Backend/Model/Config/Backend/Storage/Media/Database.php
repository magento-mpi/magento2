<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Backend\Storage\Media;

class Database extends \Magento\Core\Model\Config\Value
{
    /**
     * Core file storage
     *
     * @var \Magento\Core\Helper\File\Storage
     */
    protected $_coreFileStorage = null;

    /**
     * @param \Magento\Core\Helper\File\Storage $coreFileStorage
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage $coreFileStorage,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreFileStorage = $coreFileStorage;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Create db structure
     *
     * @return \Magento\Backend\Model\Config\Backend\Storage\Media\Database
     */
    protected function _afterSave()
    {
        $helper = $this->_coreFileStorage;
        $helper->getStorageModel(null, array('init' => true));

        return $this;
    }
}
