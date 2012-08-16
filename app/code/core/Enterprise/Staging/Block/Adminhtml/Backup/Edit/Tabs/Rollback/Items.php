<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging Rollback Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_Rollback_Items extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Configuration of grid columns
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Rollback_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('itemCheckbox', array(
            'header'    => '',
            'index'     => 'id',
            'type'      => 'checkbox',
            'truncate'  => 1000,
            'width'     => 20,
            'sortable'  => false,
            'disabled_values' => $this->getDisabledRows(),
            'field_name' => 'map[]'
        ));

        $this->addColumn('name', array(
            'header'    => 'Item Name',
            'index'     => 'name',
            'type'      => 'text',
            'sortable'  => false
        ));

        $this->addColumn('availability_text', array(
            'header'    => 'Rollback availability',
            'index'     => 'availability_text',
            'type'      => 'text',
            'sortable'  => false,
            'frame_callback' => array($this, 'frameAvailabilityField')
        ));

        Mage::dispatchEvent('adminhtml_staging_backup_edit_tab_rollback_after_prepare_columns', array('block' => $this));

        return $this;
    }

    /**
     * Retrieve codes of disabled items in colelction
     *
     * @return array
     */
    public function getDisabledRows()
    {
        return $this->getCollection()->getDisabledItemCodes();
    }

    /**
     * Retrieve codes of all items in colelction
     *
     * @return array
     */
    public function getAllRows()
    {
        return $this->getCollection()->getItemCodes();
    }


    /**
     * Prepare items collection
     * used in such way instead of standard _prepareCollection
     * bc we need collection preloaded in _prepareColumns
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Item_Xml_Collection
     */
    public function getCollection()
    {
        if (!$this->hasData('collection')) {
            $collection = Mage::getResourceModel('Enterprise_Staging_Model_Resource_Staging_Item_Xml_Collection')
                ->fillCollectionWithStagingItems($this->getExtendInfo());

            $this->setData('collection', $collection);
        }
        return $this->getData('collection');
    }

    /**
     * Callback function to put html frame (with styles) arround rendered value
     *
     * @param $renderedValue
     * @param $row
     * @param $column
     * @param $isExport
     * @return string
     */
    public function frameAvailabilityField($renderedValue, $row, $column, $isExport)
    {
        if ($row->getDisabled()) {
            $class = 'not-available';
        } else {
            $class = 'available';
        }

        return '<span class="' . $class . '">' . $renderedValue . '</span>';
    }
}

