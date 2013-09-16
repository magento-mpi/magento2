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
 * RMA observer
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Observer
{
    /**
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * @param Magento_Rma_Helper_Data $rmaData
     */
    public function __construct(
        Magento_Rma_Helper_Data $rmaData
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
