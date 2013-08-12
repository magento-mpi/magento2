<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml  system templates grid block
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Set default text when no templates found
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('templatesGrid');
        $this->setEmptyText($this->__('No Templates Found'));
    }

    /**
     * Loads and sets collection for grid
     *
     * @return Saas_PrintedTemplate_Model_Resource_Template_Collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Saas_PrintedTemplate_Model_Resource_Template_Collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares columns for grid: id, name, created_at, updated_at, type, actions
     *
     * @return Saas_PrintedTemplate_Block_Adminhtml_Template_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template_id',
            array(
                  'header' => $this->__('ID'),
                  'index'  =>'template_id'
            )
        );
        $this->addColumn('name',
            array(
                'header' => $this->__('Template Name'),
                'index'  => 'name'
        ));
        $this->addColumn('created_at',
            array(
                'header'    => $this->__('Date Added'),
                'index'     => 'created_at',
                'gmtoffset' => true,
                'type'      => 'datetime'
        ));
        $this->addColumn('updated_at',
            array(
                'header'    => $this->__('Date Updated'),
                'index'     => 'updated_at',
                'gmtoffset' => true,
                'type'      => 'datetime'
        ));
        $this->addColumn('entity_type',
            array(
                'header'   => $this->__('Template Type'),
                'index'    => 'entity_type',
                'filter'   => 'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid_Filter_Type',
                'renderer' => 'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid_Renderer_Type'
        ));
        $this->addColumn('action',
            array(
                'header'   => $this->__('Action'),
                'index'    => 'template_id',
                'sortable' => false,
                'filter'   => false,
                'width'    => '100px',
                'renderer' => 'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid_Renderer_Action'
        ));

        return $this;
    }

    /**
     * Returns url for edit template
     *
     * @param Magento_Object $row
     * @return string
     * @see Magento_Adminhtml_Block_Widget_Grid::getRowUrl()
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}

