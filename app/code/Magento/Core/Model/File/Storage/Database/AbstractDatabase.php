<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\File\Storage\Database;

/**
 * Class AbstractDatabase
 */
abstract class AbstractDatabase extends \Magento\Core\Model\AbstractModel
{
    /**
     * Store media base directory path
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * Core file storage database
     *
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * Date model
     *
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Stdlib\DateTime\DateTime $dateModel
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param string|null $connectionName
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Stdlib\DateTime\DateTime $dateModel,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        $connectionName = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_app = $app;
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_date = $dateModel;
        if (!$connectionName) {
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
        $connectionName = $this->_app->getConfig()
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
     * @return $this
     */
    public function prepareStorage()
    {
        $this->_getResource()->createDatabaseScheme();

        return $this;
    }

    /**
     * Specify connection name
     *
     * @param  string $connectionName
     * @return $this
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
