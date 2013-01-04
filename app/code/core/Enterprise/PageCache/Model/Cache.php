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
    protected $_cache;

    /**
     * Instantiate cache and prepare the "full_page_cache" directory if needed
     *
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(Mage_Core_Model_App $app, Mage_Core_Model_Config $config, Mage_Core_Model_Dir $dirs)
    {
        Magento_Profiler::start('enterprise_page_cache_create', array(
            'group' => 'enterprise_page_cache',
            'operation' => 'enterprise_page_cache:create'
        ));

        $options = $config->getNode('global/full_page_cache');
        if ($options) {
            $options = $options->asArray();
            foreach (array('backend_options', 'slow_backend_options') as $tag) {
                if (!empty($options[$tag]['cache_dir'])) {
                    $dir = $dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . DS . $options[$tag]['cache_dir'];
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }
                    $options[$tag]['cache_dir'] = $dir;
                }
            }
            $this->_cache = Mage::getModel('Mage_Core_Model_Cache', array('options' => $options));
        } else {
            $this->_cache = $app->getCacheInstance();
        }

        Magento_Profiler::stop('enterprise_page_cache_create');
    }

    /**
     * @return Mage_Core_Model_Cache
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Cache instance static getter (legacy)
     *
     * @return Mage_Core_Model_Cache
     */
    public static function getCacheInstance()
    {
        /** @var $self Enterprise_PageCache_Model_Cache */
        $self = Mage::getObjectManager()->get('Enterprise_PageCache_Model_Cache');
        return $self->getCache();
    }
}
