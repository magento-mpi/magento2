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

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Array of admin users in system
     * @var array
     */
    protected $_usersHash = null;

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('version_number');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * Prepares versions collection
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */
    protected function _prepareCollection()
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();

        $collection = Mage::getModel('enterprise_cms/page_version')->getCollection()
            ->addPageFilter($this->getPage())
            ->addVisibilityFilter($userId,
                Mage::getSingleton('enterprise_cms/config')->getAllowedAccessLevel());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare versions grid columns
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */
    protected function _prepareColumns()
    {
        $this->addColumn('version_number', array(
            'header' => Mage::helper('enterprise_cms')->__('Version #'),
            'width' => 100,
            'index' => 'version_number',
            'type' => 'options',
            'options' => Mage::helper('enterprise_cms')->getVersionsArray($this->getPage())
        ));

        $this->addColumn('label', array(
            'header' => Mage::helper('enterprise_cms')->__('Label'),
            'index' => 'label',
            'type' => 'text'
        ));

        $this->addColumn('owner', array(
            'header' => Mage::helper('enterprise_cms')->__('Owner'),
            'index' => 'user_id',
            'type' => 'options',
            'options' => Mage::helper('enterprise_cms')->getUsersArray(),
            'width' => 250
        ));

        $this->addColumn('access_level', array(
            'header' => Mage::helper('enterprise_cms')->__('Access Level'),
            'index' => 'access_level',
            'type' => 'options',
            'width' => 100,
            'options' => Mage::getSingleton('enterprise_cms/config')->getStatuses()
        ));

        $this->addColumn('revisions', array(
            'header' => Mage::helper('enterprise_cms')->__('Revisions'),
            'index' => 'revisions_count',
            'type' => 'number'
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
     * @return Mage_Cms_Model_Page
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
        return Mage::helper('enterprise_cms')->__('Versions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Versions');
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
     * Prepare massactions for this grid.
     * For now it is only ability to remove versions
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('version_id');
        $this->getMassactionBlock()->setFormFieldName('version');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('enterprise_cms')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDeleteVersions', array('_current' => true)),
             'confirm' => Mage::helper('enterprise_cms')->__('Are you sure?')
        ));
        return $this;
    }
}
