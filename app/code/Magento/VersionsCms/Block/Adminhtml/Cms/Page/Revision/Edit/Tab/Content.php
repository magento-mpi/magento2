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
 * Mian tab with cms page attributes and some modifications to CE version
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Content
    extends Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Content
{
    /**
     * Cms data
     *
     * @var Magento_VersionsCms_Helper_Data
     */
    protected $_cmsData = null;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_VersionsCms_Helper_Data $cmsData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_VersionsCms_Helper_Data $cmsData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($formFactory, $coreData, $context, $data);
    }

    /**
     * Preparing form by adding extra fields.
     * Adding on change js call.
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Content
     */
    protected function _prepareForm()
    {
        /* @var $model Magento_Cms_Model_Page */
        $model = Mage::registry('cms_page');

        parent::_prepareForm();

        $this->_cmsData->addOnChangeToFormElements($this->getForm(), 'dataChanged();');

        /* @var $fieldset Magento_Data_Form_Element_Fieldset */
        $fieldset = $this->getForm()->getElement('content_fieldset');

        if ($model->getPageId()) {
            $fieldset->addField('page_id', 'hidden', array(
                'name' => 'page_id',
            ));

            $fieldset->addField('version_id', 'hidden', array(
                'name' => 'version_id',
            ));

            $fieldset->addField('revision_id', 'hidden', array(
                'name' => 'revision_id',
            ));

            $fieldset->addField('label', 'hidden', array(
                'name' => 'label',
            ));

            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
            ));
        }

        $this->getForm()->setValues($model->getData());

        // setting current user id for new version functionality.
        // in posted data there will be current user
        $this->getForm()->getElement('user_id')->setValue(Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId());

        return $this;
    }

    /**
     * Check permission for passed action
     * Rewrite CE save permission to EE save_revision
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        if ($action == 'Magento_Cms::save') {
            $action = 'Magento_VersionsCms::save_revision';
        }
        return parent::_isAllowedAction($action);
    }
}
