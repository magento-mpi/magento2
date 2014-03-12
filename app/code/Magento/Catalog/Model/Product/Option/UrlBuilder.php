<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Option;

class UrlBuilder
{
    /**
     * @var \Magento\UrlInterface
     */
    protected $_frontendUrlBuilder;

    /**
     * @param \Magento\UrlInterface $frontendUrlBuilder
     */
    public function __construct(\Magento\UrlInterface $frontendUrlBuilder)
    {
        $this->_frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * @param string|null $route
     * @param array|null $params
     * @return string
     */
    public function getUrl($route, $params)
    {
        return $this->_frontendUrlBuilder->getUrl($route, $params);
    }
}
