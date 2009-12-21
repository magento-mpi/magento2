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
     * Retrieve value of given field and website from config
     *
     * @param string $section
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getConfigValue($section, $field, $websiteId = null)
    {
        return Mage::app()->getConfig()->getNode($section . $field, 'website', $websiteId);
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
}
