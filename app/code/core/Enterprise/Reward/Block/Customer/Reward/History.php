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
 * Customer account reward history block
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_Reward_History extends Mage_Core_Block_Template
{
    /**
     * Preparing global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'reward.history.pager')
            ->setCollection($this->getRewardHistory());
        $this->setChild('pager', $pager);

        return parent::_prepareLayout();
    }

    /**
     * Pager HTML getter
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Customer ID getter
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }

    /**
     * Check if history can be shown to customer
     *
     * @return bool
     */
    public function canShow()
    {
        return Mage::helper('enterprise_reward')->isEnabled()
            && Mage::getStoreConfigFlag('enterprise_reward/general/publish_history');
    }

    /**
     * Return reword points update history collection by customer and website
     *
     * @return Enterprise_Reward_Model_Mysql4_Reward_History_Collection
     */
    public function getRewardHistory()
    {
        if (! $this->getCollection()) {
            $this->setCollection(Mage::getModel('enterprise_reward/reward_history')
                ->getCollection()
                ->addCustomerFilter($this->getCustomerId())
                ->addWebsiteFilter(Mage::app()->getWebsite()->getId())
                ->setOrder('created_at', 'DESC'));
        }

        return $this->getCollection();
    }
}
