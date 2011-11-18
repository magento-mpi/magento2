<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * PaypalUk transaction session namespace
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PaypalUk_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('paypaluk');
    }
}
