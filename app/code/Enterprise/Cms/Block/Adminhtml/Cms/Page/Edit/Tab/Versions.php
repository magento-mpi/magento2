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
 * Cms page edit form revisions tab
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
    extends Magento_Adminhtml_Block_Widget_Grid
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Array of admin users in system
     * @var array
     */
    protected $_usersHash = null;

    /**
     * Cms data
     *
     * @var Enterprise_Cms_Helper_Data
     */
    protected $_cmsData = null;

    /**
     * @param Enterprise_Cms_Helper_Data $cmsData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Enterprise_Cms_Helper_Data $cmsData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('versions');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepares collection of versions
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */
    protected function _prepareCollection()
    {
        $userId = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId();

        /* var $collection Enterprise_Cms_Model_Resource_Version_Collection */
        $collection = Mage::getModel('Enterprise_Cms_Model_Page_Version')->getCollection()
            ->addPageFilter($this->getPage())
            ->addVisibilityFilter($userId,
                Mage::getSingleton('Enterprise_Cms_Model_Config')->getAllowedAccessLevel())
            ->addUserColumn()
            ->addUserNameColumn();

        if (!$this->getParam($this->getVarNameSort())) {
            $collection->addNumberSort();
        }

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
     * Prepare versions grid columns
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */
    protected function _prepareColumns()
    {
/*
        $this->addColumn('version_number', array(
            'header' => __('Version #'),
            'width' => 100,
            'index' => 'version_number',
            'type' => 'options',
            'options' => $this->_cmsData->getVersionsArray($this->getPage())
        ));
*/
        $this->addColumn('label', array(
            'header' => __('Version Label'),
            'index' => 'label',
            'type' => 'options',
            'options' => $this->getCollection()
                                ->getAsArray('label', 'label')
        ));

        $this->addColumn('owner', array(
            'header' => __('Owner'),
            'index' => 'username',
            'type' => 'options',
            'options' => $this->getCollection()->getUsersArray(false),
            'width' => 250
        ));

        $this->addColumn('access_level', array(
            'header' => __('Access Level'),
            'index' => 'access_level',
            'type' => 'options',
            'width' => 100,
            'options' => $this->_cmsData->getVersionAccessLevels()
        ));

        $this->addColumn('revisions', array(
            'header' => __('Quantity'),
            'index' => 'revisions_count',
            'type' => 'number'
        ));

        $this->addColumn('created_at', array(
            'width'     => 150,
            'header'    => __('Created'),
            'index'     => 'created_at',
            'type'      => 'datetime',
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
        return $this->getUrl('*/*/versions', array('_current'=>true));
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
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Versions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Versions');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare massactions for this grid.
     * For now it is only ability to remove versions
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */
    protected function _prepareMassaction()
    {
        if (Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserDeleteVersion()) {
            $this->setMassactionIdField('version_id');
            $this->getMassactionBlock()->setFormFieldName('version');

            $this->getMassactionBlock()->addItem('delete', array(
                'label'    => __('Delete'),
                'url'      => $this->getUrl('*/*/massDeleteVersions', array('_current' => true)),
                'confirm'  => __('Are you sure?'),
                'selected' => true,
            ));
        }
        return $this;
    }

    /**
     * Grid row event edit url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/cms_page_version/edit', array('page_id' => $row->getPageId(), 'version_id' => $row->getVersionId()));
    }
}
