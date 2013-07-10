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
 * Newsletter options type
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Newsletter_Block_Subscribe_Grid_Options_Status implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Newsletter_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Newsletter_Helper_Data $newsletterHelper
     */
    public function __construct(Mage_Newsletter_Helper_Data $newsletterHelper)
    {
        $this->_helper = $newsletterHelper;
    }

    /**
     * Return status column options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => $this->_helper->__('Not Activated'),
            Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => $this->_helper->__('Subscribed'),
            Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => $this->_helper->__('Unsubscribed'),
            Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED => $this->_helper->__('Unconfirmed'),
        );
    }
}
