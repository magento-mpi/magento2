<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
            ['basename' => $row->getBasename()]
        ) . '">' . $row->getBasename() . '</a>';
    }
}
