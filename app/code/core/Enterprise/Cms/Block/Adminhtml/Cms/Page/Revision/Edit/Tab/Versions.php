<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tab with version information and all available versions
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Versions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/cms/page/revision/version.phtml');
    }

    /**
     * Prepare form to edit version
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Versions
     */
    protected function _prepareForm()
    {
        /** @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_page');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save_revision')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /*
         * Determine if user owner of this revision
         */
        $userCanEditVersion = true;
        if ($model->getVersionUserId() != Mage::getSingleton('admin/session')->getUser()->getId()) {
            $userCanEditVersion = false;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('version_fieldset',
            array('legend' => Mage::helper('enterprise_cms')->__('Version Information'),
            'class' => 'fieldset-wide'));

        $fieldset->addField('create_new_version', 'checkbox', array(
            'name'      => 'create_new_version',
            'label'     => Mage::helper('enterprise_cms')->__('Create New Version'),
            'title'     => Mage::helper('enterprise_cms')->__('Create New Version'),
            'disabled'  => $isElementDisabled,
            'value' => 1
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'version_label',
            'label'     => Mage::helper('enterprise_cms')->__('Label'),
            'title'     => Mage::helper('enterprise_cms')->__('Label'),
            'disabled'  => $isElementDisabled || !$userCanEditVersion
        ));

        $fieldset->addField('access_level', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('Access Level'),
            'title'     => Mage::helper('enterprise_cms')->__('Access Level'),
            'name'      => 'access_level',
            'options'   => array(
                    Enterprise_Cms_Model_Version::ACCESS_LEVEL_PRIVATE => Mage::helper('enterprise_cms')->__('Private'),
                    Enterprise_Cms_Model_Version::ACCESS_LEVEL_PROTECTED => Mage::helper('enterprise_cms')->__('Protected'),
                    Enterprise_Cms_Model_Version::ACCESS_LEVEL_PUBLIC => Mage::helper('enterprise_cms')->__('Public')
                ),
            'disabled'  => $isElementDisabled || !$userCanEditVersion
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Adding child grid block to layout
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Versions
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock(
                'enterprise_cms/adminhtml_cms_page_revision_edit_tab_versions_grid',
                'versions.grid'
            ));
        return parent::_prepareLayout();
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
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }

}
