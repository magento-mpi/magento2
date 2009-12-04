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
 * Reward collection
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Mysql4_Reward_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_loadWebsiteBaseCurrencyCode = false;

    /**
     * Internal construcotr
     */
    protected function _construct()
    {
        $this->_init('enterprise_reward/reward');
    }

    /**
     * Set flag to add website base currency code to items
     *
     * @param boolean $flag
     * @return Enterprise_Reward_Model_Mysql4_Reward_Collection
     */
    public function setLoadWebsiteBaseCurrencyCode($flag)
    {
        $this->_loadWebsiteBaseCurrencyCode = $flag;
        return $this;
    }

    /**
     * After load collection method.
     * Add website base currency code if flag is set to true
     *
     * @return Enterprise_Reward_Model_Mysql4_Reward_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_loadWebsiteBaseCurrencyCode) {
            foreach ($this->_items as $item) {
                $item->setBaseCurrencyCode(
                    Mage::app()->getWebsite($item->getWebsiteId())->getBaseCurrencyCode());
            }
        }
        return parent::_afterLoad();
    }
}