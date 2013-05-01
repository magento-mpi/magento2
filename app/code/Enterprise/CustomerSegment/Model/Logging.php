<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerSegment_Model_Logging
{
    /**
     * Handler for logging customer segment match
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchCustomerSegmentMatch($config, $eventModel)
    {
        $request = Mage::app()->getRequest();
        $customersQty = Mage::getModel('Enterprise_CustomerSegment_Model_Segment')->getResource()
            ->getSegmentCustomersQty($request->getParam('id'));
        return $eventModel->setInfo(
            $request->getParam('id') ?
                Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Matched %d Customers of Segment %s', $customersQty, $request->getParam('id')) :
                '-'
        );
    }
}
