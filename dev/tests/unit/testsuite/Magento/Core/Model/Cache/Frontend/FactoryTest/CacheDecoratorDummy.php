<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dummy object to test creation of decorators for cache frontend
 */
class Magento_Core_Model_Cache_Frontend_FactoryTest_CacheDecoratorDummy extends \Magento\Cache\Frontend\Decorator\Bare
{
    /**
     * @var array
     */
    protected $_params;

    /**
     * @param \Magento\Cache\FrontendInterface $frontend
     * @param array $params
     */
    public function __construct(\Magento\Cache\FrontendInterface $frontend, array $params)
    {
        parent::__construct($frontend);
        $this->_params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
}
