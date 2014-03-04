<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller;

/**
 * Payflow Advanced Checkout Controller
 */
class Payflowadvanced extends \Magento\Paypal\Controller\Payflow
{
    /**
     * Redirect block name
     * @var string
     */
    protected $_redirectBlockName = 'payflow.advanced.iframe';
}
