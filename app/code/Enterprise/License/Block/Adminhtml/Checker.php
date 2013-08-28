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

class Enterprise_License_Block_Adminhtml_Checker extends Magento_Backend_Block_Template
{
    /**
     * Number of days until the expiration of license.
     *
     * @var int
     */
    protected $_daysLeftBeforeExpired;

    /**
     * License data
     *
     * @var Enterprise_License_Helper_Data
     */
    protected $_licenseData = null;

    /**
     * @param Enterprise_License_Helper_Data $licenseData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_License_Helper_Data $licenseData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_licenseData = $licenseData;
        parent::__construct($coreData, $context, $data);
    }

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
        if ($this->_licenseData->isIoncubeLoaded() && $this->_licenseData->isIoncubeEncoded()) {
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
        if ($days < 0) {
            $message = __('Act now! Your Magento Enteprise Edition license expired. '
                . 'Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.');
        } elseif (0 == $days) {
            $message = __('It\'s not too late! Your Magento Enteprise Edition expires today. '
                . 'Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.');
        } elseif ($days < 31) {
            $message = __('Act soon! Your Magento Enteprise Edition will expire in %1 days. '
                . 'Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.',
                    $days);
        }
        return $message;
    }
}
