<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\LayeredNavigation\Block\Navigation;

interface FilterRendererInterface
{
    /**
     * Render filter
     *
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return string
     */
    public function render(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter);
}
