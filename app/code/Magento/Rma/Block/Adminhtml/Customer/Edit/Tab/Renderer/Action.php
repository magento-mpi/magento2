<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer orders grid action column item renderer
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rma_Block_Adminhtml_Customer_Edit_Tab_Renderer_Action
    extends Magento_Adminhtml_Block_Sales_Reorder_Renderer_Action
{
    /**
     * Render field HRML for column
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $actions = array();
        if ($row->getIsReturnable()) {
            $actions[] = array(
                    '@' =>  array('href' => $this->getUrl('*/rma/new', array('order_id'=>$row->getId()))),
                    '#' =>  __('Return')
            );
        }
        $link1 = parent::render($row);
        $link2 = $this->_actionsToHtml($actions);
        $separator = $link1 && $link2 ? '<span class="separator">|</span>':'';
        return  $link1 . $separator . $link2;
    }
}
