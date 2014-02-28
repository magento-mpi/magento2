<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action;

class UrlBuilder
{
    /**
     * @var \Magento\UrlInterface
     */
    protected $frontendUrlBuilder;

    /**
     * @param \Magento\UrlInterface $frontendUrlBuilder
     */
    public function __construct(\Magento\UrlInterface $frontendUrlBuilder)
    {
        $this->frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * Get action url
     *
     * @param string $routePath
     * @param string $scope
     * @param string $store
     * @return string
     */
    public function getUrl($routePath, $scope, $store)
    {
        $this->frontendUrlBuilder->setScope($scope);
        $href = $this->frontendUrlBuilder->getUrl(
            $routePath,
            array('_current' => false, '_query' => '___store=' . $store)
        );
        return $href;
    }
}
