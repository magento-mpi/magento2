<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Salesrule extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_SalesRule_Model_Resource_Rule_Collection $ruleCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_SalesRule_Model_Resource_Rule_Collection $ruleCollection,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setCollection($ruleCollection);
    }

    /**
     * Initialize grid, set defaults
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('related_salesrule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('related_salesrule_filter');
        if ($this->_getBanner() && $this->_getBanner()->getId()) {
            $this->setDefaultFilter(array('in_banner_salesrule'=>1));
        }
    }

    /**
     * Set custom filter for in banner salesrule flag
     *
     * @param string $column
     * @return Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Salesrule
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banner_salesrule') {
            $ruleIds = $this->_getSelectedRules();
            if (empty($ruleIds)) {
                $ruleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.rule_id', array('in'=>$ruleIds));
            } else {
                if ($ruleIds) {
                    $this->getCollection()->addFieldToFilter('main_table.rule_id', array('nin'=>$ruleIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Create grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banner_salesrule', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_banner_salesrule',
            'values'    => $this->_getSelectedRules(),
            'align'     => 'center',
            'index'     => 'rule_id'
        ));
        $this->addColumn('salesrule_rule_id', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('salesrule_name', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Rule'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('salesrule_from_date', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Start on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('salesrule_to_date', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('End on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('salesrule_is_active', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Ajax grid URL getter
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/salesRuleGrid', array('_current'=>true));
    }

    /**
     * Get selected rules ids for in banner salesrule flag
     *
     * @return array
     */
    protected function _getSelectedRules()
    {
        $rules = $this->getSelectedSalesRules();
        if (is_null($rules)) {
            $rules = $this->getRelatedSalesRule();
        }
        return $rules;
    }

    /**
     * Get related sales rules by current banner
     *
     * @return array
     */
    public function getRelatedSalesRule()
    {
        return $this->_getBanner()->getRelatedSalesRule();
    }

    /**
     * Get current banner model
     *
     * @return Enterprise_Banner_Model_Banner
     */
    protected function _getBanner()
    {
        return Mage::registry('current_banner');
    }
}
