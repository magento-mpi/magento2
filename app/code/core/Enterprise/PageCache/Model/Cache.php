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
class Enterprise_PageCache_Model_Cache
{
    const REQUEST_MESSAGE_GET_PARAM = 'frontend_message';

    /**
     * FPC cache instance
     *
     * @var Mage_Core_Model_Cache
     */
    static protected $_cache = null;

    /**
     * Cache instance static getter
     *
     * @return Mage_Core_Model_Cache
     */
    static public function getCacheInstance()
    {
        if (is_null(self::$_cache)) {
            Magento_Profiler::start('enterprise_page_cache_create', array(
                'group' => 'enterprise_page_cache',
                'operation' => 'enterprise_page_cache:create'
            ));

            $options = Mage::app()->getConfig()->getNode('global/full_page_cache');
            if (!$options) {
                self::$_cache = Mage::app()->getCacheInstance();
                return self::$_cache;
            }

            $options = $options->asArray();

            foreach (array('backend_options', 'slow_backend_options') as $tag) {
                if (!empty($options[$tag]['cache_dir'])) {
                    $dir = Mage::getBaseDir(Mage_Core_Model_Dir::VAR_DIR) . DS . $options[$tag]['cache_dir'];
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }
                    $options[$tag]['cache_dir'] = $dir;
                }
            }

            self::$_cache = Mage::getModel('Mage_Core_Model_Cache', array('options' => $options));

            Magento_Profiler::stop('enterprise_page_cache_create');
        }

        return self::$_cache;
    }
}
