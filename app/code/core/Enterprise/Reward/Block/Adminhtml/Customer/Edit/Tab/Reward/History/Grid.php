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
 * Reward History grid
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('rewardPointsHistoryGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Enterprise_Reward_Model_Mysql4_Reward_History_Collection */
        $collection = Mage::getModel('enterprise_reward/reward_history')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('enterprise_reward')->__('Date'),
            'align'     => 'left',
            'index'     => 'created_at',
        ));

        $this->addColumn('website', array(
            'header'  => Mage::helper('enterprise_reward')->__('Website'),
            'index'   => 'website_id',
            'type'    => 'options',
            'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash()
        ));

        $this->addColumn('points_balance', array(
            'header'  => Mage::helper('enterprise_reward')->__('Points Balance'),
            'align'   => 'center',
            'index'   => 'points_balance'
        ));

        $this->addColumn('currency_amount', array(
            'header'   => Mage::helper('enterprise_reward')->__('Currency Amount'),
            'type'     => 'currency',
            'currency' => 'base_currency_code',
            'rate'     => 1,
            'index'    => 'currency_amount',
            'sortable' => false,
        ));

        $this->addColumn('points_delta', array(
            'header'  => Mage::helper('enterprise_reward')->__('Points Delta'),
            'align'   => 'center',
            'index'   => 'points_delta'
        ));

        $this->addColumn('currency_delta', array(
            'header'   => Mage::helper('enterprise_reward')->__('Currency Delta'),
            'type'     => 'currency',
            'currency' => 'base_currency_code',
            'rate'     => 1,
            'index'    => 'currency_delta',
            'sortable' => false,
        ));

        $this->addColumn('rate_description', array(
            'header'   => Mage::helper('enterprise_reward')->__('Rate Description'),
            'index'    => 'rate_description',
            'sortable' => false
        ));

        $this->addColumn('message', array(
            'header' => Mage::helper('enterprise_reward')->__('Message'),
            'align'  => 'left',
            'index'  => 'message',
            'getter' => 'getMessage'
        ));

        $this->addColumn('comment', array(
            'header' => Mage::helper('enterprise_reward')->__('Comments'),
            'align' => 'left',
            'index' => 'comment'
        ));

        return parent::_prepareColumns();
    }
}