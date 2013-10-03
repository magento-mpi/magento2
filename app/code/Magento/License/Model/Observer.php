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
     * License data
     *
     * @var \Magento\License\Helper\Data
     */
    protected $_licenseData = null;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\License\Helper\Data $licenseData
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\License\Helper\Data $licenseData
    ) {
        $this->authSession = $authSession;
        $this->_licenseData = $licenseData;
    }

    /**
     * Calculates the balance period of the license after (in days) admin authenticate in the backend.
     * 
     * @return void
     */
    public function adminUserAuthenticateAfter()
    {
        if ($this->_licenseData->isIoncubeLoaded() && $this->_licenseData->isIoncubeEncoded()) {
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
        if ($this->_licenseData->isIoncubeLoaded() && $this->_licenseData->isIoncubeEncoded()) {
            $lastCalculation = $this->authSession->getDaysLeftBeforeExpired();

            $dayOfLastCalculation = date('d', $lastCalculation['updatedAt']);

            $currentDay = date('d');

            $isComeNewDay = ($currentDay != $dayOfLastCalculation);

            if (!$this->authSession->hasDaysLeftBeforeExpired()
                || $isComeNewDay) {
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
        if ($this->_licenseData->isIoncubeLoaded() && $this->_licenseData->isIoncubeEncoded()) {
            $licenseProperties = $this->_licenseData->getIoncubeLicenseProperties();
            $expiredDate = (string)$licenseProperties[self::EXPIRED_DATE_KEY]['value'];

            $expiredYear = (int)(substr($expiredDate, 0, 4));
            $expiredMonth = (int)(substr($expiredDate, 4, 2));
            $expiredDay = (int)(substr($expiredDate, 6, 2));

            $expiredTimestamp = mktime(0, 0, 0, $expiredMonth, $expiredDay, $expiredYear);

            $daysLeftBeforeExpired = floor(($expiredTimestamp - time()) / 86400);

            $this->authSession->setDaysLeftBeforeExpired(
                array(
                    'daysLeftBeforeExpired' => $daysLeftBeforeExpired,
                    'updatedAt' => time()
                )
            );
        }
    }
}
