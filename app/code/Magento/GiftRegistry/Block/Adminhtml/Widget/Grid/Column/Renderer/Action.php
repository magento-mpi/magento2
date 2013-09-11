<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for gift registry items grid action column
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Action
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render gift registry item action as select html element
     *
     * @param  \Magento\Object $row
     * @return string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $select = $this->getLayout()->createBlock('\Magento\Core\Block\Html\Select')
            ->setId($this->getColumn()->getId())
            ->setName('items[' . $row->getItemId() . '][action]')
            ->setOptions($this->getColumn()->getOptions());
        return $select->getHtml();
    }
}
