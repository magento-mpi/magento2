<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Column renderer for gift registry items grid action column
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render gift registry item action as select html element
     *
     * @param  \Magento\Framework\Object $row
     * @return string
     */
    protected function _getValue(\Magento\Framework\Object $row)
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setId(
            $this->getColumn()->getId()
        )->setName(
            'items[' . $row->getItemId() . '][action]'
        )->setOptions(
            $this->getColumn()->getOptions()
        );
        return $select->getHtml();
    }
}
