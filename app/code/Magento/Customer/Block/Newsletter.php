<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer front  newsletter manage block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Block_Newsletter extends Magento_Customer_Block_Account_Dashboard // Magento_Core_Block_Template
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
