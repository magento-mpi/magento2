<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * Paypal transaction session namespace
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize session namespace
     *
     * @param string $sessionName
     */
    public function __construct($sessionName = null)
    {
        $this->init('paypal', $sessionName);
    }
}
