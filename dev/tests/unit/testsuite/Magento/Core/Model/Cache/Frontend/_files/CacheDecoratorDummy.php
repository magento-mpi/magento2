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
class CacheDecoratorDummy extends Magento_Cache_Frontend_Decorator_Bare
{
    /**
     * @var array
     */
    protected $_params;

    /**
     * @param Magento_Cache_FrontendInterface $frontend
     * @param array $params
     */
    public function __construct(Magento_Cache_FrontendInterface $frontend, array $params)
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
