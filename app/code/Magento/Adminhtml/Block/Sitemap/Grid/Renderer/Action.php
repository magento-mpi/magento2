<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sitemap grid action column renderer
 *
 * @category   Magento
 * @package    Magento_Sitemap
 */
namespace Magento\Adminhtml\Block\Sitemap\Grid\Renderer;

class Action extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Action
{
    public function render(\Magento\Object $row)
    {
        $this->getColumn()->setActions(array(array(
            'url'     => $this->getUrl('*/sitemap/generate', array('sitemap_id' => $row->getSitemapId())),
            'caption' => __('Generate'),
        )));
        return parent::render($row);
    }
}
