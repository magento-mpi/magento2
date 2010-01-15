<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward Helper
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SECTION_GENERAL = 'enterprise_reward/general/';
    const XML_PATH_SECTION_POINTS = 'enterprise_reward/points/';
    const XML_PATH_SECTION_NOTIFICATIONS = 'enterprise_reward/notification/';

    protected $_expiryConfig;
    protected $_hasRates = true;

    /**
     * Setter for hasRates flag
     *
     * @param boolean $flag
     * @return Enterprise_Reward_Helper_Data
     */
    public function setHasRates($flag)
    {
        $this->_hasRates = $flag;
        return $this;
    }

    /**
     * Getter for hasRates flag
     *
     * @return boolean
     */
    public function getHasRates()
    {
        return $this->_hasRates;
    }

    /**
     * Check whether reward module is enabled in system config
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('enterprise_reward/general/is_enabled');
    }

    /**
     * Check whether reward module is enabled in system config on front per website
     *
     * @param integer $websiteId
     * @return boolean
     */
    public function isEnabledOnFront($websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }
        return ($this->isEnabled() && $this->getGeneralConfig('is_enabled_on_front', (int)$websiteId));
    }

    /**
     * Retrieve value of given field and website from config
     *
     * @param string $section
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getConfigValue($section, $field, $websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }
        return (string)Mage::app()->getConfig()->getNode($section . $field, 'website', (int)$websiteId);
    }

    /**
     * Retrieve config value from General section
     *
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getGeneralConfig($field, $websiteId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECTION_GENERAL, $field, $websiteId);
    }

    /**
     * Retrieve config value from Points section
     *
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getPointsConfig($field, $websiteId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECTION_POINTS, $field, $websiteId);
    }

    /**
     * Retrieve config value from Notification section
     *
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getNotificationConfig($field, $websiteId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECTION_NOTIFICATIONS, $field, $websiteId);
    }

    /**
     * Return acc array of websites expiration points config
     *
     * @return array
     */
    public function getExpiryConfig()
    {
        if ($this->_expiryConfig === null) {
            $result = array();
            foreach (Mage::app()->getWebsites() as $website) {
                $websiteId = $website->getId();
                $result[$websiteId] = new Varien_Object(array(
                    'expiration_days' => $this->getGeneralConfig('expiration_days', $websiteId),
                    'expiry_calculation' => $this->getGeneralConfig('expiry_calculation', $websiteId),
                    'expiry_day_before' => $this->getNotificationConfig('expiry_day_before', $websiteId)
                ));
            }
            $this->_expiryConfig = $result;
        }

        return $this->_expiryConfig;
    }

    /**
     * Format (add + or - sign) before given points count
     *
     * @param integer $points
     * @return string
     */
    public function formatPointsDelta($points)
    {
        $formatedPoints = $points;
        if ($points > 0) {
            $formatedPoints = '+'.$points;
        } elseif ($points < 0) {
            $formatedPoints = '-'.(-1*$points);
        }
        return $formatedPoints;
    }

    /**
     * Getter for "Learn More" landing page URL
     *
     * @return string
     */
    public function getLandingPageUrl()
    {
        $pageIdentifier = Mage::getStoreConfig('enterprise_reward/general/landing_page');
        return Mage::getUrl('', array('_direct' => $pageIdentifier));
    }

    /**
     * Render a reward message as X points Y money
     *
     * @param int $points
     * @param string $pointsFormat
     * @param float|null $amount
     * @param string $amountFormat
     */
    public function formatReward($points, $pointsFormat = '%s', $amount = null, $amountFormat = '%s')
    {
        $points = sprintf($pointsFormat, $points);
        if (null !== $amount) {
            $amount = sprintf($amountFormat, $this->formatAmount($amount));
            return $this->__('%s reward points (%s)', $points, $amount);
        }
        return $this->__('%s reward points', $points);
    }

    /**
     * Format an amount as currency or rounded value
     *
     * @param float|string|null $amount
     * @param bool $asCurrency
     * @return string|null
     */
    public function formatAmount($amount, $asCurrency = true)
    {
        if (null === $amount) {
            return  null;
        }
        return $asCurrency ? Mage::helper('core')->currency($amount) : sprintf('%.2F', $amount);
    }

    /**
     * Format points to currency rate
     *
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function formatRateToCurrency($points, $amount, $currencyCode = null)
    {
        return $this->_formatRate('%1$s points = %2$s', $points, $amount, $currencyCode);
    }

    /**
     * Format currency to points rate
     *
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function formatRateToPoints($points, $amount, $currencyCode = null)
    {
        return $this->_formatRate('%2$s = %1$s points', $points, $amount, $currencyCode);
    }

    /**
     * Format rate according to format
     *
     * @param string $format
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    protected function _formatRate($format, $points, $amount, $currencyCode)
    {
        $points = (int)$points;
        if (!$currencyCode) {
            $amountFormatted = sprintf('%.2F', $amount);
        } else {
            $amountFormatted = Mage::app()->getLocale()->currency($currencyCode)->toCurrency((float)$amount);
        }
        return sprintf($format, $points, $amountFormatted);
    }
}
