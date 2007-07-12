<?php
class Mage_Adminhtml_Block_Tax_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/rate_collection')->loadRatesWithAttributes();

        $this->setCollection($collection);
        /*
        echo "DEBUG: <pre>";
        print_r($collection->load(1));
        echo "</pre>";
        */

        $this->addColumn('region_name',
            array(
                'header'=>__('State'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'region_name'
            )
        );

        $this->addColumn('county_name',
            array(
                'header'=>__('County'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'county_name',
                'default' => __('Any')
            )
        );

        $this->addColumn('zip_code',
            array(
                'header'=>__('Zip code'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'tax_zip_code',
                'default' => __('Any')
            )
        );

        $rateTypes = Mage::getResourceModel('tax/rate_collection')->loadRateTypes()->getItems();

        $index = 0;
        foreach( $rateTypes as $type ) {
            $this->addColumn("tax_type_{$index}",
                array(
                    'header'=>$type->getTypeName(),
                    'align' =>'left',
                    'filter'    =>false,
                    'index' => "rate_value{$index}",
                    'default' => __('N/A')
                )
            );
            $index++;
        }

       $actionsUrl = Mage::getUrl('*/*/');
       $this->addColumn('grid_actions',
            array(
                'header'=>__('Actions'),
                'width'=>10,
                'sortable'=>false,
                'filter'    =>false,
                'format' => '<a href="' . $actionsUrl .'edit/rate/$tax_rate_id">' . __('Edit') . '</a>&nbsp;
                             <a href="' . $actionsUrl .'delete/rate/$tax_rate_id">' . __('Delete') . '</a>'
            )
        );

        return parent::_prepareCollection();
    }
}