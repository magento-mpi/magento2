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
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Admin Targer Rules Grid
 */
class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('TargetRuleGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_TargetRule_Block_Adminhtml_Targetrule_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Enterprise_TargetRule_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('Enterprise_TargetRule_Model_Rule')
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Retrieve URL for Row click
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'    => $row->getId()
        ));
    }

    /**
     * Define grid columns
     *
     * @return Enterprise_TargetRule_Block_Adminhtml_Targetrule_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('ID'),
            'index'     => 'rule_id',
            'type'      => 'text',
            'width'     => 20,
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Rule Name'),
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Date Starts'),
            'index'     => 'from_date',
            'type'      => 'date',
            'default'   => '--',
            'width'     => 160,
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Date Ends'),
            'index'     => 'to_date',
            'type'      => 'date',
            'default'   => '--',
            'width'     => 160,
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Priority'),
            'index'     => 'sort_order',
            'type'      => 'text',
            'width'     => 1,
        ));

        $this->addColumn('apply_to', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Applies To'),
            'align'     => 'left',
            'index'     => 'apply_to',
            'type'      => 'options',
            'options'   => Mage::getSingleton('Enterprise_TargetRule_Model_Rule')->getAppliesToOptions(),
            'width'     => 150,
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Status'),
            'align'     => 'left',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Active'),
                0 => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Inactive'),
            ),
            'width'     => 1,
        ));

        return $this;
    }
}
