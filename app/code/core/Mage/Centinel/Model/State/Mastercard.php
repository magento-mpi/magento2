<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract Validation State Model for Mastercard
 */
class Mage_Centinel_Model_State_Mastercard extends Mage_Centinel_Model_StateAbstract
{
    /**
     * Analyse lookup`s results. If lookup is successful return true and false if it failure
     * Result depends from flag self::getIsModeStrict()
     *
     * @return bool
     */
    public function isLookupSuccessful()
    {
        if ($this->_isLookupStrictSuccessful()) {
            return true;
        } elseif (!$this->getIsModeStrict() && $this->_isLookupSoftSuccessful()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Analyse lookup`s results. If it has require params for authenticate, return true
     *
     * @return bool
     */
    public function isAuthenticateAllowed()
    {
        return $this->_isLookupStrictSuccessful();
    }

    /**
     * Analyse authenticate`s results. If authenticate is successful return true and false if it failure
     * Result depends from flag self::getIsModeStrict()
     *
     * @return bool
     */
    public function isAuthenticateSuccessful()
    {
        //Test cases 1-4, 10
        if ($this->_isLookupStrictSuccessful()) {

           if ($this->getAuthenticatePaResStatus() == 'Y' && $this->getAuthenticateEciFlag() == '02' &&
               $this->getAuthenticateXid() != '' && $this->getAuthenticateCavv() != '' &&
               $this->getAuthenticateErrorNo() == '') {
                //Test case 1
                if ($this->getAuthenticateSignatureVerification() == 'Y') {
                    return true;
                }
                //Test case 2
                if ($this->getAuthenticateSignatureVerification() == 'N') {
                    return false;
                }
            }

            //Test case 3
            if ($this->getAuthenticatePaResStatus() == 'N' && $this->getAuthenticateSignatureVerification() == 'Y' &&
                $this->getAuthenticateEciFlag() == '01' && $this->getAuthenticateXid() != '' &&
                $this->getAuthenticateCavv() == '' && $this->getAuthenticateErrorNo() == '') {
                return false;
            }

            //Test case 4
            if ($this->getAuthenticatePaResStatus() == 'U' && $this->getAuthenticateSignatureVerification() == 'Y' &&
                $this->getAuthenticateEciFlag() == '01' && $this->getAuthenticateXid() != '' &&
                $this->getAuthenticateCavv() == '' && $this->getAuthenticateErrorNo() == '') {
                if ($this->getIsModeStrict()) {
                return false;
                } else {
                    return true;
                }
            }

            //Test case 10
            if ($this->getAuthenticatePaResStatus() == '' && $this->getAuthenticateSignatureVerification() == '' &&
                $this->getAuthenticateEciFlag() == '01' && $this->getAuthenticateXid() == '' &&
                $this->getAuthenticateCavv() == '' && $this->getAuthenticateErrorNo() == '1050') {
                if ($this->getIsModeStrict()) {
                    return false;
                } else {
                    return true;
                }
            }

        }

        //Test cases 5-9
        if (!$this->getIsModeStrict() && $this->_isLookupSoftSuccessful()) {
                if ($this->getAuthenticatePaResStatus() == '' && $this->getAuthenticateSignatureVerification() == '' &&
                    $this->getAuthenticateEciFlag() == '' && $this->getAuthenticateXid() == '' &&
                    $this->getAuthenticateCavv() == '' && $this->getAuthenticateErrorNo() == '') {
                    return true;
                }
        }

        return false;
    }

    /**
     * Analyse lookup`s results. If lookup is strict successful return true
     *
     * @return bool
     */
    private function _isLookupStrictSuccessful()
    {
        //Test cases 1-4, 10
        if ($this->getLookupEnrolled() == 'Y' &&
            $this->getLookupAcsUrl() != '' &&
            $this->getLookupPayload() != '' &&
            $this->getLookupErrorNo() == '') {
            return true;
        }
        return false;
    }

    /**
     * Analyse lookup`s results. If lookup is soft successful return true
     *
     * @return bool
     */
    private function _isLookupSoftSuccessful()
    {
        //Test cases 6,7
        if ($this->getLookupAcsUrl() == '' && $this->getLookupPayload() == '' && $this->getLookupErrorNo() == '' &&
            ($this->getLookupEnrolled() == 'N' || $this->getLookupEnrolled() == 'U')) {
            return true;
        }

        //Test case 5
        if ($this->getLookupEnrolled() == '' && $this->getLookupAcsUrl() == '' &&
            $this->getLookupPayload() == '' && $this->getLookupErrorNo() == 'Timeout number') {
            return true;
        }

        //Test cases 8,9
        if ($this->getLookupEnrolled() == 'U' && $this->getLookupAcsUrl() == '' &&
            $this->getLookupPayload() == '' && $this->getLookupErrorNo() == '1001') {
            return true;
        }

        return false;
    }

}

