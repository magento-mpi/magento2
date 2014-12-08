<?php
/**
 * Catalog layer filter renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\LayeredNavigation\Block\Navigation;

use Magento\Framework\View\Element\Template;

class FilterRenderer extends \Magento\Framework\View\Element\Template implements
    \Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface
{
    /**
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return string
     */
    public function render(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        $this->assign('filterItems', $filter->getItems());
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }
}
