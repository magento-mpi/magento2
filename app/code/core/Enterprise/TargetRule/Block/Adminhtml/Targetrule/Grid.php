<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Retrieve URL for Row click
     *
     * @param Varien_Object $row
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
            'escape'    => true
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Date Start'),
            'index'     => 'from_date',
            'type'      => 'date',
            'default'   => '--',
            'width'     => 160,
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Date Expire'),
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
            'header'    => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('BugsCoverage'),
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
