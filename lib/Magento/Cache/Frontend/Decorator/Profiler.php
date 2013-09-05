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
 * Cache frontend decorator that performs profiling of cache operations
 */
namespace Magento\Cache\Frontend\Decorator;

class Profiler extends \Magento\Cache\Frontend\Decorator\Bare
{
    /**
     * Backend class prefixes to be striped from profiler tags
     *
     * @var array
     */
    private $_backendPrefixes = array();

    /**
     * @param \Magento\Cache\FrontendInterface $frontend
     * @param array $backendPrefixes Backend class prefixes to be striped for profiling informativeness
     */
    public function __construct(\Magento\Cache\FrontendInterface $frontend, $backendPrefixes = array())
    {
        parent::__construct($frontend);
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
            'frontend_type' => get_class($this->getLowLevelFrontend()),
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
        $result = get_class($this->getBackend());
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
    public function test($identifier)
    {
        \Magento\Profiler::start('cache_test', $this->_getProfilerTags('test'));
        $result = parent::test($identifier);
        \Magento\Profiler::stop('cache_test');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function load($identifier)
    {
        \Magento\Profiler::start('cache_load', $this->_getProfilerTags('load'));
        $result = parent::load($identifier);
        \Magento\Profiler::stop('cache_load');
        return $result;
    }

    /**
     * Enforce marking with a tag
     *
     * {@inheritdoc}
     */
    public function save($data, $identifier, array $tags = array(), $lifeTime = null)
    {
        \Magento\Profiler::start('cache_save', $this->_getProfilerTags('save'));
        $result = parent::save($data, $identifier, $tags, $lifeTime);
        \Magento\Profiler::stop('cache_save');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($identifier)
    {
        \Magento\Profiler::start('cache_remove', $this->_getProfilerTags('remove'));
        $result = parent::remove($identifier);
        \Magento\Profiler::stop('cache_remove');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function clean($mode = \Zend_Cache::CLEANING_MODE_ALL, array $tags = array())
    {
        \Magento\Profiler::start('cache_clean', $this->_getProfilerTags('clean'));
        $result = parent::clean($mode, $tags);
        \Magento\Profiler::stop('cache_clean');
        return $result;
    }
}
