<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full page cache
 *
 * @category   Enterprise
 * @package    Enterprise_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Model_Cache extends Mage_Core_Model_Cache
{
    const REQUEST_MESSAGE_GET_PARAM = 'frontend_message';

    /**
     * FPC cache instance
     *
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param bool $banCache
     * @param array $options
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $config,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Factory_Helper $helperFactory,
        $banCache = false,
        array $options = array()
    )
    {
        Magento_Profiler::start('enterprise_page_cache_create', array(
            'group' => 'enterprise_page_cache',
            'operation' => 'enterprise_page_cache:create'
        ));

        $configOptions = $config->getNode('global/full_page_cache');
        if ($configOptions) {
            $configOptions = $configOptions->asArray();
        } else {
            $configOptions = array();
        }
        $options = array_merge($configOptions, $options);
        if ($options) {
            foreach (array('backend_options', 'slow_backend_options') as $tag) {
                if (!empty($options[$tag]['cache_dir'])) {
                    $dir = $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . DS . $options[$tag]['cache_dir'];
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }
                    $options[$tag]['cache_dir'] = $dir;
                }
            }
        }
        parent::__construct($config, $dirs, $helperFactory, $banCache, $options);
        Magento_Profiler::stop('enterprise_page_cache_create');
    }
}
