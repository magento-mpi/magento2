<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Redirect to GoogleCheckout
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleCheckout\Block;

class Redirect extends \Magento\Page\Block\Redirect
{
    /**
     *  Get target URL
     *
     *  @return string
     */
    public function getTargetURL ()
    {
        return $this->getRedirectUrl();
    }


    public function getMethod ()
    {
        return 'GET';
    }

    public function getMessage ()
    {
        return __('You will be redirected to GoogleCheckout in a few seconds.');
    }
}
