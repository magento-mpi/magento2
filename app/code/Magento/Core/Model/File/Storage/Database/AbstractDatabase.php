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
 * Abstract database storage model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\File\Storage\Database;

abstract class AbstractDatabase extends \Magento\Core\Model\File\Storage\AbstractStorage
{
    /**
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($coreFileStorageDb, $context, $registry, $resource, $resourceCollection, $data);
        $connectionName = (isset($data['connection'])) ? $data['connection'] : null;
        if (empty($connectionName)) {
            $connectionName = $this->getConfigConnectionName();
        }

        $this->setConnectionName($connectionName);
    }

    /**
     * Retrieve connection name saved at config
     *
     * @return string
     */
    public function getConfigConnectionName()
    {
        $connectionName = \Mage::app()->getConfig()
            ->getValue(\Magento\Core\Model\File\Storage::XML_PATH_STORAGE_MEDIA_DATABASE, 'default');
        if (empty($connectionName)) {
            $connectionName = 'default_setup';
        }

        return $connectionName;
    }

    /**
     * Get resource instance
     *
     * @return \Magento\Core\Model\Resource\AbstractResource
     */
    protected function _getResource()
    {
        $resource = parent::_getResource();
        $resource->setConnectionName($this->getConnectionName());

        return $resource;
    }

    /**
     * Prepare data storage
     *
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function prepareStorage()
    {
        $this->_getResource()->createDatabaseScheme();

        return $this;
    }

    /**
     * Specify connection name
     *
     * @param  $connectionName
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function setConnectionName($connectionName)
    {
        if (!empty($connectionName)) {
            $this->setData('connection_name', $connectionName);
            $this->_getResource()->setConnectionName($connectionName);
        }

        return $this;
    }
}
