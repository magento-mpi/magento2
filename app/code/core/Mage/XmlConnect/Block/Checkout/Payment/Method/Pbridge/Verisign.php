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
 * Payflow Pro through Pbridge Payment method xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Pbridge_Verisign
    extends Mage_XmlConnect_Block_Checkout_Payment_Method_Pbridge_Abstract
{
    /**
     * Payment model path
     *
     * @var string
     */
    protected $_model = 'Payflow_Pro';
}
