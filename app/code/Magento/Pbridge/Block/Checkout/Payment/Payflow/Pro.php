<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Payflow Pro payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Checkout_Payment_Payflow_Pro extends Magento_Pbridge_Block_Payment_Form_Abstract
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = 'verisign';
}
