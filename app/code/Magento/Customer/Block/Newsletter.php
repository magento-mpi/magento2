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
namespace Magento\Customer\Block;

class Newsletter extends \Magento\Customer\Block\Account\Dashboard \Magento\Customer\Block\Account\Dashboard // \Magento\Core\Block\Template
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
