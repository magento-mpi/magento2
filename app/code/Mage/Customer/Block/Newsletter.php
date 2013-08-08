<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer front  newsletter manage block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Newsletter extends Mage_Customer_Block_Account_Dashboard // Magento_Core_Block_Template
{

    protected $_template = 'form/newsletter.phtml';

    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    public function getAction()
    {
        return $this->getUrl('*/*/save');
    }

}
