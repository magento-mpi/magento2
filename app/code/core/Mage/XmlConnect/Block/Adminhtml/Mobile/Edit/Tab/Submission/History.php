<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect application history grid
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Submission_History
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Set order by column and order direction
     * Set grid ID
     * Set use AJAX for grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('history_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('history_filter');
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Submission History');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Submission History');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication();
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Initialize history collection
     * Set application filter
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_XmlConnect_Model_Resource_History_Collection')
            ->addApplicationFilter($this->_getApplication()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('activation_key', array(
            'header'    => $this->__('Activation Key'),
            'align'     => 'left',
            'index'     => 'activation_key',
            'type'      => 'text',
            'escape'    => true
        ));

        $this->addColumn('created_at', array(
            'header'    => $this->__('Date Submitted'),
            'align'     => 'left',
            'index'     => 'created_at',
            'type'      => 'datetime'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Ajax grid URL getter
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/submissionHistoryGrid', array('_current'=>true));
    }

    /**
     * Get current application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _getApplication()
    {
        return Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication();
    }

    /**
     * Remove row click url
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
