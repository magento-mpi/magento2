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
     * Add rma availability option to options column in customer's order grid
     *
     * @param Magento_Event_Observer $observer
     */
    public function addRmaOption(Magento_Event_Observer $observer)
    {
        $renderer = $observer->getEvent()->getRenderer();
        /** @var $row Magento_Sales_Model_Order */
        $row = $observer->getEvent()->getRow();

        if (Mage::helper('Enterprise_Rma_Helper_Data')->canCreateRma($row, true)) {
            $reorderAction = array(
                    '@' =>  array('href' => $renderer->getUrl('*/rma/new', array('order_id'=>$row->getId()))),
                    '#' =>  __('Return')
            );
            $renderer->addToActions($reorderAction);
        }
    }
}
