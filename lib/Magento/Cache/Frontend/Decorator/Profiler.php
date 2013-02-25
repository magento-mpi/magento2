<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache frontend decorator that performs profiling of cache operations
 */
class Magento_Cache_Frontend_Decorator_Profiler implements Magento_Cache_FrontendInterface
{
    /**
     * Cache frontend instance to delegate actual cache operations to
     *
     * @var Magento_Cache_FrontendInterface
     */
    private $_frontend;

    /**
     * Backend class prefixes to be striped from profiler tags
     *
     * @var array
     */
    private $_backendPrefixes = array();

    /**
     * @param Magento_Cache_FrontendInterface $frontend
     * @param array $backendPrefixes Backend class prefixes to be striped for profiling informativeness
     */
    public function __construct(Magento_Cache_FrontendInterface $frontend, $backendPrefixes = array())
    {
        $this->_frontend = $frontend;
        $this->_backendPrefixes = $backendPrefixes;
    }

    /**
     * Retrieve profiler tags that correspond to a cache operation
     *
     * @param string $operation
     * @return array
     */
    protected function _getProfilerTags($operation)
    {
        return array(
            'group'         => 'cache',
            'operation'     => 'cache:' . $operation,
            'frontend_type' => get_class($this->_frontend->getLowLevelFrontend()),
            'backend_type'  => $this->_getBackendType(),
        );
    }

    /**
     * Get short cache backend type name by striping known backend class prefixes
     *
     * @return string
     */
    protected function _getBackendType()
    {
        $result = get_class($this->_frontend->getBackend());
        foreach ($this->_backendPrefixes as $backendClassPrefix) {
            if (substr($result, 0, strlen($backendClassPrefix)) == $backendClassPrefix) {
                $result = substr($result, strlen($backendClassPrefix));
                break;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function test($id)
    {
        Magento_Profiler::start('cache_test', $this->_getProfilerTags('test'));
        $result = $this->_frontend->test($id);
        Magento_Profiler::stop('cache_test');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        Magento_Profiler::start('cache_load', $this->_getProfilerTags('load'));
        $result = $this->_frontend->load($id);
        Magento_Profiler::stop('cache_load');
        return $result;
    }

    /**
     * Enforce marking with a tag
     *
     * {@inheritdoc}
     */
    public function save($data, $id, array $tags = array(), $lifeTime = null)
    {
        Magento_Profiler::start('cache_save', $this->_getProfilerTags('save'));
        $result = $this->_frontend->save($data, $id, $tags, $lifeTime);
        Magento_Profiler::stop('cache_save');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        Magento_Profiler::start('cache_remove', $this->_getProfilerTags('remove'));
        $result = $this->_frontend->remove($id);
        Magento_Profiler::stop('cache_remove');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        Magento_Profiler::start('cache_clean', $this->_getProfilerTags('clean'));
        $result = $this->_frontend->clean($mode, $tags);
        Magento_Profiler::stop('cache_clean');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackend()
    {
        return $this->_frontend->getBackend();
    }

    /**
     * {@inheritdoc}
     */
    public function getLowLevelFrontend()
    {
        return $this->_frontend->getLowLevelFrontend();
    }
}
