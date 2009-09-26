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
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Customer Attributes Grid
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerAttributeGrid');
    }

    /**
     * Prepare customer attributes grid collection object
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/attribute_collection')
            ->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare customer attributes grid columns
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('used_in', array(
            'header'=>Mage::helper('enterprise_customer')->__('Used In'),
            'sortable'=>true,
            'index'=>'frontend_label',
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumnAfter('visibility', array(
            'header'=>Mage::helper('enterprise_customer')->__('Visible on Frontend'),
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '0' => Mage::helper('enterprise_customer')->__('No'),
                '1' => Mage::helper('enterprise_customer')->__('Yes'),
            ),
            'align' => 'center',
        ), 'used_in');

        return $this;
    }
}
