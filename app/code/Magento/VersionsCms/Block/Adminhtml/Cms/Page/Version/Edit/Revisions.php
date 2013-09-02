<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid with revisions on version page
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
    extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Cms data
     *
     * @var Magento_VersionsCms_Helper_Data
     */
    protected $_cmsData = null;

    /**
     * @param Magento_VersionsCms_Helper_Data $cmsData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_VersionsCms_Helper_Data $cmsData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

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
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
     */
    protected function _prepareCollection()
    {
        /* var $collection Magento_VersionsCms_Model_Resource_Revision_Collection */
        $collection = Mage::getModel('Magento_VersionsCms_Model_Page_Revision')->getCollection()
            ->addPageFilter($this->getPage())
            ->addVersionFilter($this->getVersion())
            ->addUserColumn()
            ->addUserNameColumn();

            // Commented this bc now revision are shown in scope of version and not page.
            // So if user has permission to load this version he
            // has permission to see all its versions
            //->addVisibilityFilter(Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId(),
            //    Mage::getSingleton('Magento_VersionsCms_Model_Config')->getAllowedAccessLevel());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Retrieve collection for grid if there is not collection call _prepareCollection
     *
     * @return Magento_VersionsCms_Model_Resource_Page_Version_Collection
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
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
     */
    protected function _prepareColumns()
    {
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
     * @return Magento_VersionsCms_Model_Page_Version
     */
    public function getVersion()
    {
        return Mage::registry('cms_page_version');
    }

    /**
     * Prepare massactions for this grid.
     * For now it is only ability to remove revisions
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Version_Edit_Revisions
     */
    protected function _prepareMassaction()
    {
        if (Mage::getSingleton('Magento_VersionsCms_Model_Config')->canCurrentUserDeleteRevision()) {
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
