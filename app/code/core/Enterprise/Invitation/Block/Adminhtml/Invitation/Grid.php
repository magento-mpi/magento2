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
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Invitations grid
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_invitation/invitation')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('enterprise_invitation_id', array(
            'header'=> Mage::helper('enterprise_invitation')->__('ID'),
            'width' => '80px',
            'align' => 'right',
            'type'  => 'text',
            'index' => 'invitation_id'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('enterprise_invitation')->__('Email'),
            'index' => 'email',
            'type'  => 'text'
        ));

        $this->addColumn('date', array(
            'header' => Mage::helper('enterprise_invitation')->__('Sent'),
            'index' => 'date',
            'type' => 'datetime',
            'gmtoffset' => true,
            'width' => '150px'
        ));

        $this->addColumn('signup_date', array(
            'header' => Mage::helper('enterprise_invitation')->__('Registered'),
            'index' => 'signup_date',
            'type' => 'datetime',
            'gmtoffset' => true,
            'width' => '150px'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('enterprise_invitation')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('enterprise_invitation/source_invitation_status')->getOptions(),
            'width' => '140px'
        ));

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('enterprise_invitation')->__('Inviter ID'),
            'index' => 'customer_id',
            'align' => 'right',
            'width' => '80px'
        ));

        $groups = Mage::getModel('customer/group')->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group_id', array(
            'header' => Mage::helper('enterprise_invitation')->__('Invitee Group'),
            'index' => 'group_id',
            'type'  => 'options',
            'options' => $groups,
            'width' => '140px'
        ));

        $this->addColumn('referral_id', array(
            'header' => Mage::helper('enterprise_invitation')->__('Invitee ID'),
            'index' => 'referral_id',
            'align' => 'right',
            'width' => '80px'
        ));

        $this->addColumn('actions', array(
            'header'    => $this->helper('enterprise_invitation')->__('Action'),
            'width'     => '15px',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'action',
            'actions'   => array(
                array(
                    'url'       => $this->getUrl('*/*/view') . 'id/$invitation_id',
                    'caption'   => $this->helper('enterprise_invitation')->__('View'),
                ),
            )
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('invitation_id');
        $this->getMassactionBlock()->setFormFieldName('invitations');
        $this->getMassactionBlock()->addItem('cancel', array(
                'label' => $this->helper('enterprise_invitation')->__('Cancel'),
                'url' => $this->getUrl('*/*/massCancel'),
                'confirm' => Mage::helper('enterprise_invitation')->__('Are you sure you want to do this?')
        ));

        $this->getMassactionBlock()->addItem('resend', array(
                'label' => $this->helper('enterprise_invitation')->__('Re-Send'),
                'url' => $this->getUrl('*/*/massResend')
        ));

        return parent::_prepareMassaction();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }
}
