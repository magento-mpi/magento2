<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Reorder\Renderer;

/**
 * Adminhtml alert queue grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Action
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $_salesReorder = null;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Sales\Helper\Reorder $salesReorder,
        array $data = array()
    ) {
        $this->_salesReorder = $salesReorder;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $this->_actions = array();
        if ($this->_salesReorder->canReorder($row)) {
            $reorderAction = array(
                '@' => array('href' => $this->getUrl('sales/order_create/reorder', array('order_id'=>$row->getId()))),
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

    /**
     * Get escaped value
     *
     * @param string $value
     * @return string
     */
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
        $attributesObject = new \Magento\Object();

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
