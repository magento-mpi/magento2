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
 * @category   Enterprise
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('name');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_customerbalance/balance_history')
                ->getCollection()
                ->addWebsiteData()
                ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
                
        if( !Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            if( (bool) Mage::getStoreConfig('customer/account_share/scope') ) {
                $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
                $collection->addWebsiteFilter($customer->getWebsiteId());
            } else {
                $collection->addWebsiteFilter(Mage::helper('enterprise_permissions')->getRelevantWebsites());
            }
        }

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $store = Mage::app()->getStore();
        $history = Mage::getModel('enterprise_customerbalance/balance_history');

        $this->addColumn('action', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('Action'),
            'width'     => '150px',
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => $history->getActionNamesArray(),
        ));
        
        $this->addColumn('delta', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('Delta'),
            'width'     => '50px',
            'index'     => 'delta',
            'type'      => 'price',
            'sortable'  => false,
            'currency_code' => $store->getBaseCurrency()->getCode(),
        ));

        $this->addColumn('balance', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('Balance'),
            'width'     => '50px',
            'index'     => 'balance',
            'type'      => 'price',
            'sortable'  => false,
            'currency_code' => $store->getBaseCurrency()->getCode(),
        ));
        
        $this->addColumn('website_name', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('Website'),
            'index'     => 'name',
            'sortable' => false,
        ));
        
        $this->addColumn('admin_user', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('By Admin'),
            'index'     => 'admin_user',
            'sortable' => false,
        ));
        
        $this->addColumn('notified', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('Notified'),
            'index'     => 'notified',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('enterprise_customerbalance')->__('Yes'),
                '0' => Mage::helper('enterprise_customerbalance')->__('No'),
            ),
            'sortable'  => false,
        ));
        
        $this->addColumn('date', array(
            'header'    => Mage::helper('enterprise_customerbalance')->__('Date'),
            'index'     => 'date',
            'sortable'  => false,
        ));
        
        return parent::_prepareColumns();
    }
}