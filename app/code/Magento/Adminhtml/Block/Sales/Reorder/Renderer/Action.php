<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml alert queue grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Reorder_Renderer_Action
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Array to store all options data
     *
     * @var array
     */
    protected $_actions = array();

    /**
     * Sales reorder
     *
     * @var Magento_Sales_Helper_Reorder
     */
    protected $_salesReorder = null;

    /**
     * @param Magento_Sales_Helper_Reorder $salesReorder
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Helper_Reorder $salesReorder,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_salesReorder = $salesReorder;
        parent::__construct($context, $data);
    }

    public function render(Magento_Object $row)
    {
        $this->_actions = array();
        if ($this->_salesReorder->canReorder($row)) {
            $reorderAction = array(
                '@' => array('href' => $this->getUrl('*/sales_order_create/reorder', array('order_id'=>$row->getId()))),
                '#' =>  __('Reorder')
            );
            $this->addToActions($reorderAction);
        }
        $this->_eventManager->dispatch('adminhtml_customer_orders_add_action_renderer', array(
            'renderer' => $this,
            'row' => $row,
        ));
        return $this->_actionsToHtml();
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value), '\\\'');
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
