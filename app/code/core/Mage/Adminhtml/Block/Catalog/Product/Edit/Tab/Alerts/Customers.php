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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Crossell products admin grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Customers extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_alertModel;

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('catalog')->__('There are no customers for this alert'));
    }

    public function setModel(Mage_CustomerAlert_Model_Type $alertModel)
    {
        $this->_alertModel = $alertModel;
        return $this;
    }

    public function loadCustomers()
    {
        $customer = Mage::getResourceModel('customeralert/customer_collection')
            -> setAlert ($this->_alertModel);
        $this->setData('customerCollection',$customer);
        return $this;
    }

    protected function _prepareCollection()
    {
        $customerCollection = $this->getData('customerCollection');
        $this->setCollection($customerCollection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header' => Mage::helper('catalog')->__('First Name'),
            'index'  => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('catalog')->__('Last Name'),
            'index'  => 'lastname',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('catalog')->__('Email'),
            'index'  => 'email',
        ));

        $this->addColumn('last_alert_sent', array(
            'header' => Mage::helper('catalog')->__('Last Alert Sent'),
            'index'  => 'last_alert_sent',
            'type'   => 'datetime'

        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product/alertsGrid', $this->_alertModel->getParamValues());
    }
}
