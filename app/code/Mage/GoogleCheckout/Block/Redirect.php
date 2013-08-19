<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Redirect to GoogleCheckout
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleCheckout_Block_Redirect extends Mage_Page_Block_Redirect
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
