<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA observer
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Observer
{
    /**
     * Rma data
     *
     * @var Enterprise_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * @param Enterprise_Rma_Helper_Data $rmaData
     */
    public function __construct(
        Enterprise_Rma_Helper_Data $rmaData
    ) {
        $this->_rmaData = $rmaData;
    }

    /**
     * Add rma availability option to options column in customer's order grid
     *
     * @param Magento_Event_Observer $observer
     */
    public function addRmaOption(Magento_Event_Observer $observer)
    {
        $renderer = $observer->getEvent()->getRenderer();
        /** @var $row Magento_Sales_Model_Order */
        $row = $observer->getEvent()->getRow();

        if ($this->_rmaData->canCreateRma($row, true)) {
            $reorderAction = array(
                    '@' =>  array('href' => $renderer->getUrl('*/rma/new', array('order_id'=>$row->getId()))),
                    '#' =>  __('Return')
            );
            $renderer->addToActions($reorderAction);
        }
    }
}
