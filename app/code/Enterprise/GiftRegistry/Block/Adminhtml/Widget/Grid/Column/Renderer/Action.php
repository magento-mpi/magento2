<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for gift registry items grid action column
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render gift registry item action as select html element
     *
     * @param  Magento_Object $row
     * @return string
     */
    protected function _getValue(Magento_Object $row)
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setId($this->getColumn()->getId())
            ->setName('items[' . $row->getItemId() . '][action]')
            ->setOptions($this->getColumn()->getOptions());
        return $select->getHtml();
    }
}
