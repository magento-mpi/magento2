<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid with revisions on version page
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
    extends Magento_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('revisionsGrid');
        $this->setDefaultSort('revision_number');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * Prepares collection of revisions
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
     */
    protected function _prepareCollection()
    {
        /* var $collection Enterprise_Cms_Model_Resource_Revision_Collection */
        $collection = Mage::getModel('Enterprise_Cms_Model_Page_Revision')->getCollection()
            ->addPageFilter($this->getPage())
            ->addVersionFilter($this->getVersion())
            ->addUserColumn()
            ->addUserNameColumn();

            // Commented this bc now revision are shown in scope of version and not page.
            // So if user has permission to load this version he
            // has permission to see all its versions
            //->addVisibilityFilter(Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId(),
            //    Mage::getSingleton('Enterprise_Cms_Model_Config')->getAllowedAccessLevel());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Retrieve collection for grid if there is not collection call _prepareCollection
     *
     * @return Enterprise_Cms_Model_Resource_Page_Version_Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_prepareCollection();
        }

        return $this->_collection;
    }

    /**
     * Prepare event grid columns
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
     */
    protected function _prepareColumns()
    {
/*
        $this->addColumn('version_number', array(
            'header' => __('Version #'),
            'width' => 100,
            'index' => 'version_number',
            'type' => 'options',
            'options' => Mage::helper('Enterprise_Cms_Helper_Data')->getVersionsArray($this->getPage())
        ));

        $this->addColumn('label', array(
            'header' => __('Version Label'),
            'index' => 'label',
            'type' => 'options',
            'options' => Mage::helper('Enterprise_Cms_Helper_Data')
                                ->getVersionsArray('label', 'label', $this->getPage())
        ));

        $this->addColumn('access_level', array(
            'header' => __('Access Level'),
            'index' => 'access_level',
            'type' => 'options',
            'width' => 100,
            'options' => Mage::helper('Enterprise_Cms_Helper_Data')->getVersionAccessLevels()
        ));
*/
        $this->addColumn('revision_number', array(
            'header' => __('Revision'),
            'width' => 200,
            'type' => 'number',
            'index' => 'revision_number'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Created'),
            'index' => 'created_at',
            'type' => 'datetime',
            'filter_time' => true,
            'width' => 250
        ));

        $this->addColumn('author', array(
            'header' => __('Author'),
            'index' => 'user',
            'type' => 'options',
            'options' => $this->getCollection()->getUsersArray()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare url for reload grid through ajax
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/revisions', array('_current'=>true));
    }

    /**
     * Grid row event edit url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/cms_page_revision/edit', array('page_id' => $row->getPageId(), 'revision_id' => $row->getRevisionId()));
    }

    /**
     * Returns cms page object from registry
     *
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        return Mage::registry('cms_page');
    }

    /**
     * Returns cms page version object from registry
     *
     * @return Enterprise_Cms_Model_Page_Version
     */
    public function getVersion()
    {
        return Mage::registry('cms_page_version');
    }

    /**
     * Prepare massactions for this grid.
     * For now it is only ability to remove revisions
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
     */
    protected function _prepareMassaction()
    {
        if (Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserDeleteRevision()) {
            $this->setMassactionIdField('revision_id');
            $this->getMassactionBlock()->setFormFieldName('revision');

            $this->getMassactionBlock()->addItem('delete', array(
                 'label'=> __('Delete'),
                 'url'  => $this->getUrl('*/*/massDeleteRevisions', array('_current' => true)),
                 'confirm' => __('Are you sure?')
            ));
        }
        return $this;
    }
}
