<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Tax_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('tax_rule_id');
        $this->setId('taxRuleGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Tax_Model_Calculation_Rule')
            ->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        if ($this->getCollection()) {
            $this->getCollection()
                ->addCustomerTaxClassesToResult()
                ->addProductTaxClassesToResult()
                ->addRatesToResult();
        }
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            switch ($column->getId()) {
                case 'tax_rates':
                    $this->getCollection()->joinCalculationData('rate');
                    break;

                case 'customer_tax_classes':
                    $this->getCollection()->joinCalculationData('ctc');
                    break;

                case 'product_tax_classes':
                    $this->getCollection()->joinCalculationData('ptc');
                    break;

            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('code',
            array(
                'header'=>Mage::helper('Mage_Tax_Helper_Data')->__('Name'),
                'align' =>'left',
                'index' => 'code',
                'filter_index' => 'code',
            )
        );

        $this->addColumn('customer_tax_classes',
            array(
                'header'=>Mage::helper('Mage_Tax_Helper_Data')->__('Customer Tax Class'),
                'sortable'  => false,
                'align' =>'left',
                'index' => 'customer_tax_classes',
                'filter_index' => 'ctc.customer_tax_class_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('Mage_Tax_Model_Class')->getCollection()->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)->toOptionHash(),
            )
        );

        $this->addColumn('product_tax_classes',
            array(
                'header'=>Mage::helper('Mage_Tax_Helper_Data')->__('Product Tax Class'),
                'sortable'  => false,
                'align' =>'left',
                'index' => 'product_tax_classes',
                'filter_index' => 'ptc.product_tax_class_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('Mage_Tax_Model_Class')->getCollection()->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->toOptionHash(),
            )
        );

        $this->addColumn('tax_rates',
            array(
                'sortable'  => false,
                'header'  => Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rate'),
                'align'   => 'left',
                'index'   => 'tax_rates',
                'filter_index' => 'rate.tax_calculation_rate_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('Mage_Tax_Model_Calculation_Rate')->getCollection()->toOptionHashOptimized(),
            )
        );

        $this->addColumn('priority',
            array(
                'header'=>Mage::helper('Mage_Tax_Helper_Data')->__('Priority'),
                'width' => '50px',
                'index' => 'priority'
            )
        );

        $this->addColumn('position',
            array(
                'header'=>Mage::helper('Mage_Tax_Helper_Data')->__('Sort Order'),
                'width' => '50px',
                'index' => 'position'
            )
        );

        $actionsUrl = $this->getUrl('*/*/');

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('rule' => $row->getId()));
    }

}
