<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_License
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * License Adminhtml Block
 *
 * @category   Enterprise
 * @package    Enterprise_License
 */

class Enterprise_License_Block_Adminhtml_Checker extends Magento_Adminhtml_Block_Template
{
    /**
     * Number of days until the expiration of license.
     *
     * @var int
     */
    protected $_daysLeftBeforeExpired;

    /**
     * Сounts the number of days remaining until the expiration of license.
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $data = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getDaysLeftBeforeExpired();
        $this->_daysLeftBeforeExpired = $data['daysLeftBeforeExpired'];
    }

    /**
     * Decides it's time to show warning or not.
     *
     * @return bool
     */
    public function shouldDispalyNotification()
    {
        $enterprise_license=Mage::helper('Enterprise_License_Helper_Data');
        if($enterprise_license->isIoncubeLoaded() && $enterprise_license->isIoncubeEncoded()) {
            return ($this->_daysLeftBeforeExpired < 31);
        } else {
            return false;
        }
    }


    /**
     * Getter: return counts of days remaining until the expiration of license.
     *
     * @return int
     */
    public function getDaysLeftBeforeExpired()
    {
        return $this->_daysLeftBeforeExpired;
    }

    /**
     * Returns the text to be displayed in the message.
     *
     * @return string
     */
    public function getMessage()
    {
        $message = "";

        $days = $this->getDaysLeftBeforeExpired();

        if($days < 0) {
            $message = __('Act now! Your Magento Enteprise Edition license expired. Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.');
        } elseif(0 == $days) {
            $message = __('It\'s not too late! Your Magento Enteprise Edition expires today. Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.');
        } elseif($days < 31) {
            $message = __('Act soon! Your Magento Enteprise Edition will expire in %1 days. Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.', $days);
        }

        return $message;
    }
}
