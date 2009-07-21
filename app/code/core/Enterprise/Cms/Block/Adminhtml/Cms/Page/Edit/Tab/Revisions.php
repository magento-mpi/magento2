<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms page edit form revisions tab
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Revisions
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Array of available versions for user
     * @var array
     */
    protected $_versionsHash = null;

    /**
     * Array of admin users in system
     * @var array
     */
    protected $_usersHash = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('revisionsGrid');
        $this->setDefaultSort('revision_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * Prepares events collection
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_cms/revision')->getCollection()
            ->addPageFilter($this->getPage())
            ->joinVersions()
            ->addVisibilityFilter(Mage::getSingleton('admin/session')->getUser()->getId(),
                Mage::getSingleton('enterprise_cms/config')->getAllowedAccessLevel());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare event grid columns
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('revision_id', array(
            'header' => $this->__('Revision'),
            'width' => 100,
            'type' => 'text',
            'index' => 'revision_id'
        ));

        $this->addColumn('created_at', array(
            'header' => $this->__('Created'),
            'index' => 'created_at',
            'type' => 'datetime',
            'filter_time' => true,
            'width' => 150
        ));

        $this->addColumn('version', array(
            'header' => $this->__('Version'),
            'index' => 'version_id',
            'type' => 'options',
            'options' => $this->_getVersions()
        ));

        $this->addColumn('access_level', array(
            'header' => $this->__('Access Level'),
            'index' => 'access_level',
            'type' => 'options',
            'width' => 100,
            'options' => array(
                    Enterprise_Cms_Model_Version::ACCESS_LEVEL_PRIVATE => $this->__('Private'),
                    Enterprise_Cms_Model_Version::ACCESS_LEVEL_PROTECTED => $this->__('Protected'),
                    Enterprise_Cms_Model_Version::ACCESS_LEVEL_PUBLIC => $this->__('Public')
                )
        ));

        $this->addColumn('author', array(
            'header' => $this->__('Author'),
            'index' => 'user_id',
            'type' => 'options',
            'options' => $this->_getUsers()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve array of version available for current user
     *
     * @return array
     */
    protected function _getVersions()
    {
        if (!$this->_versionsHash) {
            $userId = Mage::getSingleton('admin/session')->getUser()->getId();
            $collection = Mage::getModel('enterprise_cms/version')->getCollection()
                ->addVersionLabelToSelect()
                ->addVisibilityFilter($userId,
                    Mage::getSingleton('enterprise_cms/config')->getAllowedAccessLevel());

            $this->_versionsHash = $collection->getIdLabelArray();
        }

        return $this->_versionsHash;
    }

    /**
     * Retrieve array of admin users in system
     *
     * @return array
     */
    protected function _getUsers()
    {
        if (!$this->_usersHash) {
            $collection = Mage::getModel('admin/user')->getCollection();
            $this->_usersHash = array();
            foreach ($collection as $user) {
                $this->_usersHash[$user->getId()] = $user->getUsername();
            }
        }

        return $this->_usersHash;
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
        return $this->getUrl('*/*/edit', array('page_id' => $row->getPageId(), 'revision_id' => $row->getRevisionId()));
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_cms')->__('Revisions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Revisions');
    }

    /**
     * Returns status flag about this tab can be showen or not
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
     * Returns cms page object from registry
     *
     * @return Mage_Cms_ModelPage
     */
    public function getPage()
    {
        return Mage::registry('cms_page');
    }
}
