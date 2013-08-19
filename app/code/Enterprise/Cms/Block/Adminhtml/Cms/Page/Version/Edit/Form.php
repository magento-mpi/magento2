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
 * Form for version edit page
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Version_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    protected $_template = 'page/version/form.phtml';

    /**
     * Preparing from for version page
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('_current' => true)),
                'method' => 'post'
            ));

        $form->setUseContainer(true);

        /* @var $model Magento_Cms_Model_Page */
        $version = Mage::registry('cms_page_version');

        $config = Mage::getSingleton('Enterprise_Cms_Model_Config');
        /* @var $config Enterprise_Cms_Model_Config */

        $isOwner = $config->isCurrentUserOwner($version->getUserId());
        $isPublisher = $config->canCurrentUserPublishRevision();

        $fieldset = $form->addFieldset('version_fieldset',
            array('legend' => __('Version Information'),
            'class' => 'fieldset-wide'));

        $fieldset->addField('version_id', 'hidden', array(
            'name'      => 'version_id'
        ));

        $fieldset->addField('page_id', 'hidden', array(
            'name'      => 'page_id'
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => __('Version Label'),
            'disabled'  => !$isOwner,
            'required'  => true
        ));

        $fieldset->addField('access_level', 'select', array(
            'label'     => __('Access Level'),
            'title'     => __('Access Level'),
            'name'      => 'access_level',
            'options'   => Mage::helper('Enterprise_Cms_Helper_Data')->getVersionAccessLevels(),
            'disabled'  => !$isOwner && !$isPublisher
        ));

        if ($isPublisher) {
            $fieldset->addField('user_id', 'select', array(
                'label'     => __('Owner'),
                'title'     => __('Owner'),
                'name'      => 'user_id',
                'options'   => Mage::helper('Enterprise_Cms_Helper_Data')->getUsersArray(!$version->getUserId()),
                'required'  => !$version->getUserId()
            ));
        }

        $form->setValues($version->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
