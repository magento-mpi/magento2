<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPalUK Direct through Pbridge Payment method xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Pbridge_Paypaluk
    extends Mage_XmlConnect_Block_Checkout_Payment_Method_Pbridge_Abstract
{
    /**
     * Payment model path
     *
     * @var string
     */
    protected $_model = 'Paypaluk';
}
