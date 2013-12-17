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
namespace Magento\Sitemap\Block\Adminhtml\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    public function render(\Magento\Object $row)
    {
        $this->getColumn()->setActions(array(array(
            'url'     => $this->getUrl('adminhtml/sitemap/generate', array('sitemap_id' => $row->getSitemapId())),
            'caption' => __('Generate'),
        )));
        return parent::render($row);
    }
}
