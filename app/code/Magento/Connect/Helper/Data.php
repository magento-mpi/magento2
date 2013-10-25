<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Connect\Helper;

/**
 * Default helper of the module
 */
class Data extends \Magento\Core\Helper\Data
{
    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * Application dirs
     *
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * @var \Magento\Convert\Xml
     */
    protected $_xmlConverter;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\App\State $appState
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Convert\Xml $xmlConverter
     * @param \Magento\App\Dir $dirs
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\Core\Model\Date $dateModel,
        \Magento\App\State $appState,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\Filesystem $filesystem,
        \Magento\Convert\Xml $xmlConverter,
        \Magento\App\Dir $dirs,
        $dbCompatibleMode = true
    ) {
        $this->_filesystem = $filesystem;
        $this->_dirs = $dirs;
        $this->_xmlConverter = $xmlConverter;
        parent::__construct($context, $eventManager, $config, $coreStoreConfig, $storeManager,
            $locale, $dateModel, $appState, $encryptor, $dbCompatibleMode
        );
    }

    /**
     * Retrieve file system path for local extension packages
     * Return path with last directory separator
     *
     * @return string
     */
    public function getLocalPackagesPath()
    {
        return $this->_dirs->getDir('var') . DS . 'connect' . DS;
    }

    /**
     * Retrieve file system path for local extension packages (for version 1 packages only)
     * Return path with last directory separator
     *
     * @return string
     */
    public function getLocalPackagesPathV1x()
    {
        return $this->_dirs->getDir('var') . DS . 'pear' . DS;
    }

    /**
     * Retrieve a map to convert a channel from previous version of Magento Connect Manager
     *
     * @return array
     */
    public function getChannelMapFromV1x()
    {
        return array(
            'connect.magentocommerce.com/community' => 'community',
            'connect.magentocommerce.com/core' => 'community'
        );
    }

    /**
     * Retrieve a map to convert a channel to previous version of Magento Connect Manager
     *
     * @return array
     */
    public function getChannelMapToV1x()
    {
        return array(
            'community' => 'connect.magentocommerce.com/community'
        );
    }

    /**
     * Convert package channel in order for it to be compatible with current version of Magento Connect Manager
     *
     * @param string $channel
     *
     * @return string
     */
    public function convertChannelFromV1x($channel)
    {
        $channelMap = $this->getChannelMapFromV1x();
        if (isset($channelMap[$channel])) {
            $channel = $channelMap[$channel];
        }
        return $channel;
    }

    /**
     * Convert package channel in order for it to be compatible with previous version of Magento Connect Manager
     *
     * @param string $channel
     *
     * @return string
     */
    public function convertChannelToV1x($channel)
    {
        $channelMap = $this->getChannelMapToV1x();
        if (isset($channelMap[$channel])) {
            $channel = $channelMap[$channel];
        }
        return $channel;
    }

    /**
     * Load local package data array
     *
     * @param string $packageName without extension
     * @return array|false
     */
    public function loadLocalPackage($packageName)
    {
        //check LFI protection
        $this->_filesystem->checkLfiProtection($packageName);

        $path = $this->getLocalPackagesPath();
        $xmlFile = $path . $packageName . '.xml';
        $serFile = $path . $packageName . '.ser';

        if ($this->_filesystem->isFile($xmlFile) && $this->_filesystem->isReadable($xmlFile)) {
            $xml  = simplexml_load_string($this->_filesystem->read($xmlFile));
            $data = $this->_xmlConverter->xmlToAssoc($xml);
            if (!empty($data)) {
                return $data;
            }
        }

        if ($this->_filesystem->isFile($serFile) && $this->_filesystem->isReadable($xmlFile)) {
            $data = unserialize($this->_filesystem->read($serFile));
            if (!empty($data)) {
                return $data;
            }
        }

        return false;
    }
}
