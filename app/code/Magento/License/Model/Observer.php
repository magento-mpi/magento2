<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_License
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * License observer
 *
 * @category   Magento
 * @package    Magento_License
 */
namespace Magento\License\Model;

class Observer
{
    /**
     * Key to retrieve the license expiration date from the properties of the license.
     *
     */
    const EXPIRED_DATE_KEY = 'expiredOn';

    /**
     * Calculates the balance period of the license after (in days) admin authenticate in the backend.
     * 
     * @return void
     */
    public function adminUserAuthenticateAfter()
    {
        $magento_license=Mage::helper('Magento\License\Helper\Data');
        if($magento_license->isIoncubeLoaded() && $magento_license->isIoncubeEncoded()) {
            $this->_calculateDaysLeftToExpired();
        }
    }

    /**
     * Checks the presence of the calculation results (balance period of the license, in days) in the
     * session after admin authenticate in the backend.
     * If the data is not there or they are obsolete for one day, it does recalculate.
     *
     * @return void
     */
    public function preDispatch()
    {
        $magento_license=Mage::helper('Magento\License\Helper\Data');
        if($magento_license->isIoncubeLoaded() && $magento_license->isIoncubeEncoded()) {
            $lastCalculation = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getDaysLeftBeforeExpired();

            $dayOfLastCalculation = date('d', $lastCalculation['updatedAt']);

            $currentDay = date('d');

            $isComeNewDay = ($currentDay != $dayOfLastCalculation);

            if(!\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->hasDaysLeftBeforeExpired() or $isComeNewDay) {
                $this->_calculateDaysLeftToExpired();
            }
        }
    }

    /**
     * Calculates the number of days before the expiration of the license.
     * Keeps the results of calculation and computation time in the session.
     * 
     * @return void
     */
    protected function _calculateDaysLeftToExpired()
    {
        $magento_license=Mage::helper('Magento\License\Helper\Data');
        if($magento_license->isIoncubeLoaded() && $magento_license->isIoncubeEncoded()) {
            $licenseProperties = \Mage::helper('Magento\License\Helper\Data')->getIoncubeLicenseProperties();
            $expiredDate = (string)$licenseProperties[self::EXPIRED_DATE_KEY]['value'];

            $expiredYear = (int)(substr($expiredDate, 0, 4));
            $expiredMonth = (int)(substr($expiredDate, 4, 2));
            $expiredDay = (int)(substr($expiredDate, 6, 2));

            $expiredTimestamp = mktime(0, 0, 0, $expiredMonth, $expiredDay, $expiredYear);

            $daysLeftBeforeExpired = floor(($expiredTimestamp - time()) / 86400);

            \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->setDaysLeftBeforeExpired(
                array(
                    'daysLeftBeforeExpired' => $daysLeftBeforeExpired,
                    'updatedAt' => time()
                )
            );
        }
    }
}
