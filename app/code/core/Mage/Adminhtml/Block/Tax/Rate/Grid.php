<?php
class Mage_Adminhtml_Block_Tax_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('region_name');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/rate_collection')->addAttributes();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('region_name',
            array(
                'header'=>__('State'),
                'align' =>'left',
                'index' => 'region_name'
            )
        );

        $this->addColumn('county_name',
            array(
                'header'=>__('County'),
                'align' =>'left',
                'index' => 'county_name',
                'default' => __('*')
            )
        );

        $this->addColumn('zip_code',
            array(
                'header'=>__('Zip/Post Code'),
                'align' =>'left',
                'index' => 'tax_zip_code',
                'default' => __('*')
            )
        );

        $rateTypes = Mage::getResourceModel('tax/rate_type_collection')->load()->getItems();

        $index = 0;
        foreach( $rateTypes as $type ) {
            $this->addColumn("tax_type_{$index}",
                array(
                    'header'=>$type->getTypeName(),
                    'align' =>'left',
                    'filter' => false,
                    'index' => "rate_value{$index}",
                    'default' => __('N/A')
                )
            );
            $index++;
        }

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('rate' => $row->getTaxRateId()));
    }
}
