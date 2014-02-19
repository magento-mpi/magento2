<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Controller;

/**
 * Interface ExpressRedirectInterface
 * @package Magento\Checkout\Controller\Express
 */
interface ExpressRedirectInterface
{
    /**
     * Redirect to login page
     */
    public function redirectLogin();

    /**
     * Does method supports before auth url for redirect
     * @return boolean
     */
    public function supportsCustomerBeforeAuthUrl();
} 