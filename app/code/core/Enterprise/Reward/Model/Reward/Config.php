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
 * Reward config model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Reward_Config extends Varien_Object
{
    protected $_xmlPathPointsConfig = 'enterprise_reward/points/';

    /**
     * Retrieve points delta by given action and website from config
     *
     * @param integer $action
     * @param integer $websiteId
     * @return integer
     */
    public function getPointsDeltaByAction($action, $websiteId)
    {
        $points = 0;
        $field = '';
        switch ($action) {
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_REVIEW:
                $field = 'review';
                break;
            case Enterprise_Reward_Model_Reward::REWARD_ACTION_TAG:
                $field = 'tag';
                break;
        }
        if ($field) {
            $points = $this->getConfigValue($field, $websiteId);
        }
        return $points;
    }

    /**
     * Retrieve value of given field and website from config
     *
     * @param string $field
     * @param integer $websiteId
     * @return integer
     */
    public function getConfigValue($field, $websiteId)
    {
        $points = Mage::app()->getConfig()
            ->getNode($this->_xmlPathPointsConfig . $field, 'website', (int)$websiteId);
        return (int)$points;
    }
}