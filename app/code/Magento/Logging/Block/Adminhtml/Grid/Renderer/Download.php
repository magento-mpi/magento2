<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Logging archive grid  item renderer
 *
 * @category   Magento
 * @package    Magento_Logging
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Logging\Block\Adminhtml\Grid\Renderer;

class Download
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     *
     * @return mixed
     */
    public function _getValue(\Magento\Object $row)
    {
        return '<a href="' . $this->getUrl('*/*/download', array('basename' => $row->getBasename())) . '">'
               . $row->getBasename() . '</a>';

    }
}
