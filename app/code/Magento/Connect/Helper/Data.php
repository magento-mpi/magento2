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
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Convert\Xml
     */
    protected $_xmlConverter;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $readDirectory;
    
    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\App\State $appState
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Convert\Xml $xmlConverter
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\App\State $appState,
        \Magento\App\Filesystem $filesystem,
        \Magento\Convert\Xml $xmlConverter,
        $dbCompatibleMode = true
    ) {
        $this->filesystem = $filesystem;
        $this->readDirectory = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::VAR_DIR);
        $this->_xmlConverter = $xmlConverter;
        parent::__construct(
            $context,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $appState,
            $dbCompatibleMode
        );
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
     * @return array|boolean
     */
    public function loadLocalPackage($packageName)
    {
        $xmlFile = sprintf('connect/%.xml', $packageName);
        $serFile = sprintf('connect/%.ser', $packageName);
        if ($this->readDirectory->isFile($xmlFile) && $this->readDirectory->isReadable($xmlFile)) {
            $xml = simplexml_load_string($this->readDirectory->readFile($xmlFile));
            $data = $this->_xmlConverter->xmlToAssoc($xml);
            if (!empty($data)) {
                return $data;
            }
        }
        if ($this->readDirectory->isFile($serFile) && $this->readDirectory->isReadable($xmlFile)) {
            $data = unserialize($this->readDirectory->readFile($serFile));
            if (!empty($data)) {
                return $data;
            }
        }
        return false;
    }
}
