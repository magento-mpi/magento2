<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model;

use Magento\App\ConfigInterface;
use Magento\App\Filesystem;
use Magento\Core\Model\Store\Config as StoreConfig;

/**
 * Model is responsible for replacing default vcl template
 * file configuration with user-defined from configuration
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

/**
 * Class Config
 *
 * @package Magento\PageCache\Model
 */
class Config
{
    /**
     * Cache types
     */
    const BUILT_IN = 1;

    const VARNISH = 2;

    /**
     * XML path to Varnish settings
     */
    const XML_PAGECACHE_TTL = 'system/full_page_cache/ttl';

    const XML_PAGECACHE_TYPE = 'system/full_page_cache/caching_application';

    const XML_VARNISH_PAGECACHE_ACCESS_LIST = 'system/full_page_cache/varnish/access_list';

    const XML_VARNISH_PAGECACHE_BACKEND_PORT = 'system/full_page_cache/varnish/backend_port';

    const XML_VARNISH_PAGECACHE_BACKEND_HOST = 'system/full_page_cache/varnish/backend_host';

    const XML_VARNISH_PAGECACHE_DESIGN_THEME_REGEX = 'design/theme/ua_regexp';

    /**
     * @var StoreConfig
     */
    protected $_coreStoreConfig;

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * XML path to value for saving temporary .vcl configuration
     */
    const VARNISH_CONFIGURATION_PATH = 'system/full_page_cache/varnish/path';

    /**
     * @var \Magento\App\Cache\StateInterface $_cacheState
     */
    protected $_cacheState;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_modulesDirectory;

    /**
     * @param Filesystem $filesystem
     * @param StoreConfig $coreStoreConfig
     * @param ConfigInterface $config
     * @param \Magento\App\Cache\StateInterface $cacheState
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        StoreConfig $coreStoreConfig,
        ConfigInterface $config,
        \Magento\App\Cache\StateInterface $cacheState
    ) {
        $this->_modulesDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::MODULES_DIR);
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_config = $config;
        $this->_cacheState = $cacheState;
    }

    /**
     * Return currently selected cache type: built in or varnish
     * 
     * @return int
     */
    public function getType()
    {
        return $this->_config->getValue(self::XML_PAGECACHE_TYPE);
    }

    /**
     * Return page lifetime
     *
     * @return int
     */
    public function getTtl()
    {
        return $this->_config->getValue(self::XML_PAGECACHE_TTL);
    }

    /**
     * Return generated varnish.vcl configuration file
     *
     * @return string
     */
    public function getVclFile()
    {
        $data = $this->_modulesDirectory->readFile($this->_config->getValue(self::VARNISH_CONFIGURATION_PATH));
        return strtr($data, $this->_getReplacements());
    }

    /**
     * Prepare data for VCL config
     *
     * @return array
     */
    protected function _getReplacements()
    {
        return array(
            '{{ host }}' => $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_BACKEND_HOST),
            '{{ port }}' => $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_BACKEND_PORT),
            '{{ ips }}' => $this->_getAccessList(),
            '{{ design_exceptions_code }}' => $this->_getDesignExceptions()
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
     * @return mixed|null|string
     */
    protected function _getAccessList()
    {
        $result = '';
        $tpl = "    \"%s\";";
        $accessList = $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_ACCESS_LIST);
        if (!empty($accessList)) {
            $ips = explode(', ', $accessList);
            foreach ($ips as $ip) {
                $result[] = sprintf($tpl, $ip);
            }
            return implode("\n", $result);
        }
        return $result;
    }

    /**
     * Get regexs for design exceptions
     * Different browser user-agents may use different themes
     * Varnish supports regex with internal modifiers only so
     * we have to convert "/pattern/iU" into "(?Ui)pattern"
     *
     * @return string
     */
    protected function _getDesignExceptions()
    {
        $result = '';
        $tpl = "%s (req.http.user-agent ~ \"%s\") {\n" . "        hash_data(\"%s\");\n" . "    }";
        $expressions = $this->_coreStoreConfig->getConfig(self::XML_VARNISH_PAGECACHE_DESIGN_THEME_REGEX);
        if ($expressions) {
            $rules = array_values(unserialize($expressions));
            foreach ($rules as $i => $rule) {
                if (preg_match('/^[\W]{1}(.*)[\W]{1}(\w+)?$/', $rule['regexp'], $matches)) {
                    if (!empty($matches[2])) {
                        $pattern = sprintf("(?%s)%s", $matches[2], $matches[1]);
                    } else {
                        $pattern = $matches[1];
                    }
                    $if = $i == 0 ? 'if' : ' elsif';
                    $result .= sprintf($tpl, $if, $pattern, $rule['value']);
                }
            }
        }
        return $result;
    }

    /**
     * Whether a cache type is enabled in Cache Management Grid
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_cacheState->isEnabled(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
    }
}
