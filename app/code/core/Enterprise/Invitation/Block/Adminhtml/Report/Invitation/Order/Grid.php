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
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml invitation orders report grid block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid
    extends Mage_Adminhtml_Block_Report_Grid
{

    /**
     * Prepare report collection
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('invitation/report_invitation_order_collection');
        return $this;
    }

    /**
     * Prepare report grid columns
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sent', array(
            'header'    =>Mage::helper('invitation')->__('Invitations sent'),
            'type'      =>'number',
            'index'     => 'sent',
            'width'     =>'200'
        ));

        $this->addColumn('accepted', array(
            'header'    =>Mage::helper('invitation')->__('Invitations accepted'),
            'type'      =>'number',
            'index'     => 'accepted',
            'width'     =>'200'
        ));

        $this->addColumn('purchased', array(
            'header'    =>Mage::helper('invitation')->__('Accepted and purchased'),
            'type'      =>'number',
            'index'     => 'purchased',
            'width'     =>'220'
        ));

        $this->addColumn('purchased_rate', array(
            'header'    =>Mage::helper('invitation')->__('Conversion rate'),
            'index'     =>'purchased_rate',
            'renderer'  => 'invitation/adminhtml_grid_column_renderer_percent',
            'type'      =>'string',
            'width'     =>'100'
        ));

        $this->addExportType('*/*/exportOrderCsv', Mage::helper('invitation')->__('CSV'));
        $this->addExportType('*/*/exportOrderExcel', Mage::helper('invitation')->__('Excel'));

        return parent::_prepareColumns();
    }


}