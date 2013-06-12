<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * Payment centinel session model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize session namespace
     *
     * @param string $sessionName
     */
    public function __construct($sessionName = null)
    {
        $this->init('centinel_validator', $sessionName);
    }
}
