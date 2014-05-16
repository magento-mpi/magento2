<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Logging archive grid  item renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Logging\Block\Adminhtml\Grid\Renderer;

class Download extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\Object $row
     *
     * @return string
     */
    public function _getValue(\Magento\Framework\Object $row)
    {
        return '<a href="' . $this->getUrl(
            'adminhtml/*/download',
            array('basename' => $row->getBasename())
        ) . '">' . $row->getBasename() . '</a>';
    }
}
