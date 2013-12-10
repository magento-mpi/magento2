<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Block;

/**
 * Redirect to GoogleCheckout
 */
class Redirect extends \Magento\View\Element\Redirect
{
    /**
     * URL for redirect location
     *
     * @return string URL
     */
    public function getTargetURL()
    {
        return $this->getRedirectUrl();
    }

    /**
     * HTML form method attribute
     *
     * @return string Method
     */
    public function getFormMethod()
    {
        return 'GET';
    }

    /**
     * Additional custom message
     *
     * @return string Output message
     */
    public function getMessage()
    {
        return __('You will be redirected to GoogleCheckout in a few seconds.');
    }
}
