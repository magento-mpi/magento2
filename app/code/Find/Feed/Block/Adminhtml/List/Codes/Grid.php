<?php
/**
 * {license_notice}
 *
 * @category
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TheFind feed attribute map Grid
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_List_Codes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('find_feed_list_codes');
        $this->setUseAjax(true);
    }

    /**
     * Prepare codes collection
     *
     * @return Find_Feed_Block_Adminhtml_List_Codes_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Find_Feed_Model_Resource_Codes_Collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('import_code', array(
            'header'=> __('Feed code'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'import_code'
        ));

        $this->addColumn('eav_code', array(
            'header'=> __('Eav code'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'eav_code'
        ));

        $source = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Source_Boolean');
        $isImportedOptions = $source->getOptionArray();

        $this->addColumn('is_imported', array(
            'header' => __('In Feed'),
            'width' => '100px',
            'index' => 'is_imported',
            'type'  => 'options',
            'options' => $isImportedOptions
        ));
        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('code_id');
        $this->getMassactionBlock()->setFormFieldName('code_id');

        $this->getMassactionBlock()->addItem('enable', array(
            'label'         => __('Import'),
            'url'           => $this->getUrl('*/codes_grid/massEnable'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('disable', array(
            'label'         => __('Not import'),
            'url'           => $this->getUrl('*/codes_grid/massDisable'),
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label'         => __('Delete'),
            'url'           => $this->getUrl('*/codes_grid/delete'),
        ));

        return $this;
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/codes_grid/grid', array('_current'=>true));
    }
}
