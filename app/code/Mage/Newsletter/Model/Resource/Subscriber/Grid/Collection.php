<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter subscribers grid collection
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Resource_Subscriber_Grid_Collection
    extends Mage_Newsletter_Model_Resource_Subscriber_Collection
{
    /**
     * Sets flag for customer info loading on load
     *
     * @return Mage_Newsletter_Model_Resource_Subscriber_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->showCustomerInfo(true)
            ->addSubscriberTypeField()
            ->showStoreInfo();
        return $this;
    }
}
