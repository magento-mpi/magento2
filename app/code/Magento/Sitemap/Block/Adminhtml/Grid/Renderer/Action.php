<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Block\Adminhtml\Grid\Renderer;

/**
 * Sitemap grid action column renderer
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $this->getColumn()->setActions(array(array(
            'url'     => $this->getUrl('adminhtml/sitemap/generate', array('sitemap_id' => $row->getSitemapId())),
            'caption' => __('Generate'),
        )));
        return parent::render($row);
    }
}
