<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sitemap grid action column renderer
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Magento_Object $row)
    {
        $this->getColumn()->setActions(array(array(
            'url'     => $this->getUrl('*/sitemap/generate', array('sitemap_id' => $row->getSitemapId())),
            'caption' => __('Generate'),
        )));
        return parent::render($row);
    }
}
