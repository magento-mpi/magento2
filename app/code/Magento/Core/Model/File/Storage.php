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
 * File storage model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\File;

class Storage extends \Magento\Core\Model\AbstractModel
{
    /**
     * Storage systems ids
     */
    const STORAGE_MEDIA_FILE_SYSTEM         = 0;
    const STORAGE_MEDIA_DATABASE            = 1;

    /**
     * Config pathes for storing storage configuration
     */
    const XML_PATH_STORAGE_MEDIA            = 'system/media_storage_configuration/media_storage';
    const XML_PATH_STORAGE_MEDIA_DATABASE   = 'system/media_storage_configuration/media_database';
    const XML_PATH_MEDIA_RESOURCE_WHITELIST = 'system/media_storage_configuration/allowed_resources';
    const XML_PATH_MEDIA_UPDATE_TIME        = 'system/media_storage_configuration/configuration_update_time';


    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage';

    /**
     * Core file storage
     *
     * @var \Magento\Core\Helper\File\Storage
     */
    protected $_coreFileStorage = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Core\Helper\File\Storage $coreFileStorage
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \\Magento\Data\Collection\Db $resourceCollection
     * $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage $coreFileStorage,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreFileStorage = $coreFileStorage;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Show if there were errors while synchronize process
     *
     * @param  \Magento\Core\Model\AbstractModel $sourceModel
     * @param  \Magento\Core\Model\AbstractModel $destinationModel
     * @return bool
     */
    protected function _synchronizeHasErrors(\Magento\Core\Model\AbstractModel $sourceModel,
        \Magento\Core\Model\AbstractModel $destinationModel
    ) {
        if (!$sourceModel || !$destinationModel) {
            return true;
        }

        return $sourceModel->hasErrors() || $destinationModel->hasErrors();
    }

    /**
     * Return synchronize process status flag
     *
     * @return \Magento\Core\Model\File\Storage\Flag
     */
    public function getSyncFlag()
    {
        return \Mage::getSingleton('Magento\Core\Model\File\Storage\Flag')->loadSelf();
    }

    /**
     * Retrieve storage model
     * If storage not defined - retrieve current storage
     *
     * params = array(
     *  connection  => string,  - define connection for model if needed
     *  init        => bool     - force initialization process for storage model
     * )
     *
     * @param  int|null $storage
     * @param  array $params
     * @return \Magento\Core\Model\AbstractModel|bool
     */
    public function getStorageModel($storage = null, $params = array())
    {
        if (is_null($storage)) {
            $storage = $this->_coreFileStorage->getCurrentStorageCode();
        }

        switch ($storage) {
            case self::STORAGE_MEDIA_FILE_SYSTEM:
                $model = \Mage::getModel('Magento\Core\Model\File\Storage\File');
                break;
            case self::STORAGE_MEDIA_DATABASE:
                $connection = (isset($params['connection'])) ? $params['connection'] : null;
                $arguments = array('connection' => $connection);
                $model = \Mage::getModel('Magento\Core\Model\File\Storage\Database',
                    array('connectionName' => $arguments));
                break;
            default:
                return false;
        }

        if (isset($params['init']) && $params['init']) {
            $model->init();
        }

        return $model;
    }

    /**
     * Synchronize current media storage with defined
     * $storage = array(
     *  type        => int
     *  connection  => string
     * )
     *
     * @param  array $storage
     * @return \Magento\Core\Model\File\Storage
     */
    public function synchronize($storage)
    {
        if (is_array($storage) && isset($storage['type'])) {
            $storageDest    = (int) $storage['type'];
            $connection     = (isset($storage['connection'])) ? $storage['connection'] : null;
            $helper         = $this->_coreFileStorage;

            // if unable to sync to internal storage from itself
            if ($storageDest == $helper->getCurrentStorageCode() && $helper->isInternalStorage()) {
                return $this;
            }

            $sourceModel        = $this->getStorageModel();
            $destinationModel   = $this->getStorageModel(
                $storageDest,
                array(
                    'connection'    => $connection,
                    'init'          => true
                )
            );

            if (!$sourceModel || !$destinationModel) {
                return $this;
            }

            $hasErrors = false;
            $flag = $this->getSyncFlag();
            $flagData = array(
                'source'                        => $sourceModel->getStorageName(),
                'destination'                   => $destinationModel->getStorageName(),
                'destination_storage_type'      => $storageDest,
                'destination_connection_name'   => (string) $destinationModel->getConfigConnectionName(),
                'has_errors'                    => false,
                'timeout_reached'               => false
            );
            $flag->setFlagData($flagData);

            $destinationModel->clear();

            $offset = 0;
            while (($dirs = $sourceModel->exportDirectories($offset)) !== false) {
                $flagData['timeout_reached'] = false;
                if (!$hasErrors) {
                    $hasErrors = $this->_synchronizeHasErrors($sourceModel, $destinationModel);
                    if ($hasErrors) {
                        $flagData['has_errors'] = true;
                    }
                }

                $flag->setFlagData($flagData)
                    ->save();

                $destinationModel->importDirectories($dirs);
                $offset += count($dirs);
            }
            unset($dirs);

            $offset = 0;
            while (($files = $sourceModel->exportFiles($offset, 1)) !== false) {
                $flagData['timeout_reached'] = false;
                if (!$hasErrors) {
                    $hasErrors = $this->_synchronizeHasErrors($sourceModel, $destinationModel);
                    if ($hasErrors) {
                        $flagData['has_errors'] = true;
                    }
                }

                $flag->setFlagData($flagData)
                    ->save();

                $destinationModel->importFiles($files);
                $offset += count($files);
            }
            unset($files);
        }

        return $this;
    }

    /**
     * Return current media directory, allowed resources for get.php script, etc.
     *
     * @return array
     */
    public function getScriptConfig()
    {
        $config = array();
        $config['media_directory'] = \Mage::getBaseDir('media');

        $allowedResources = $this->_coreConfig->getValue(self::XML_PATH_MEDIA_RESOURCE_WHITELIST, 'default');
        foreach ($allowedResources as $allowedResource) {
            $config['allowed_resources'][] = $allowedResource;
        }

        $config['update_time'] = $this->_coreStoreConfig->getConfig(self::XML_PATH_MEDIA_UPDATE_TIME);

        return $config;
    }
}
