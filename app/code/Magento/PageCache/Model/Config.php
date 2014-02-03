<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PageCache config model
 * Used get PageCache configuration
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model;

use Magento\App\Filesystem;


class Config extends \Magento\Object
{
    /**#@+
     * XML path to Varnish settings
     */
    const XML_VARNISH_PAGECACHE_TTL  = 'system/varnish_configuratrion_settings/pagecache_ttl';
    const XML_VARNISH_PAGECACHE_DEBUG = 'system/varnish_configuratrion_settings/pagecache_debug';
    const XML_VARNISH_PAGECACHE_ACCESS_LIST = 'system/varnish_configuratrion_settings/pagecache_access_list';
    const XML_VARNISH_PAGECACHE_BACKEND_PORT = 'system/varnish_configuratrion_settings/pagecache_backend_port';
    const XML_VARNISH_PAGECACHE_BACKEND_HOST = 'system/varnish_configuratrion_settings/pagecache_backend_host';
    /**#@-*/

    /**
     * Placeholders to replace
     *
     * @var array
     */
    protected $_placeholders = array();

    /**
     * Config data
     *
     * @var array
     */
    protected $_replacement = array();

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * Path to save temporary .vcl configuration
     *
     * @var string
     */
    protected $_path = 'Magento/PageCache/etc/varnish.vcl';

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_modulesDirectory;

    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        array $data = array())
    {
        $this->_filesystem = $filesystem;
        $this->_modulesDirectory = $this->_filesystem->getDirectoryRead(\Magento\App\Filesystem::MODULES_DIR);
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($data);
    }

    /**
     * Return generated varnish.vcl configuration file
     *
     * @return string
     */
    public function getVclFile()
    {
        $this->_prepareVclData();

        $data = $this->_modulesDirectory->readFile($this->_path);
        $data = str_replace($this->_placeholders, $this->_replacement, $data);

        return $data;
    }

    /**
     * Prepare data for VCL config
     *
     * @return array
     */
    protected function _prepareVclData()
    {
        $this->_placeholders = array(
            '{{ host }}',
            '{{ port }}',
            '{{ ips }}',
            '{{ design_exceptions_code }}'
        );

        $this->_replacement = array(
            $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_BACKEND_HOST),
            $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_BACKEND_PORT),
            $this->_getIps(),
            'get_design_exceptions_code'
        );
    }

    /**
     * Get IPs access list that can purge Varnish configuration for config file generation
     * and transform it to appropriate view
     *
     * acl purge{
     *  "127.0.0.1";
     *  "127.0.0.2";
     *
     * @return string
     */
    protected function _getIps()
    {
        $accessList = $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_ACCESS_LIST);
        if (!is_null($accessList)) {
            $ipsArray = explode(', ', $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_ACCESS_LIST));
            $accessList = implode('; ', array_map(
                function ($listElement) {
                    return '"' . $listElement . '"';
                },
                $ipsArray));
            return $accessList;
        }
    }
}
