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
 * Adminhtml alert queue grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Reorder_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Array to store all options data
     *
     * @var array
     */
    protected $_actions = array();

    public function render(Magento_Object $row)
    {
        $this->_actions = array();
        if (Mage::helper('Mage_Sales_Helper_Reorder')->canReorder($row)) {
            $reorderAction = array(
                '@' => array('href' => $this->getUrl('*/sales_order_create/reorder', array('order_id'=>$row->getId()))),
                '#' =>  __('Reorder')
            );
            $this->addToActions($reorderAction);
        }
        Mage::dispatchEvent('adminhtml_customer_orders_add_action_renderer', array('renderer' => $this, 'row' => $row));
        return $this->_actionsToHtml();
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    /**
     * Render options array as a HTML string
     *
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions = array())
    {
        $html = array();
        $attributesObject = new Magento_Object();

        if (empty($actions)) {
            $actions = $this->_actions;
        }

        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return  implode($html, '<span class="separator">|</span>');
    }

    /**
     * Add one action array to all options data storage
     *
     * @param array $actionArray
     * @return void
     */
    public function addToActions($actionArray)
    {
        $this->_actions[] = $actionArray;
    }
}
