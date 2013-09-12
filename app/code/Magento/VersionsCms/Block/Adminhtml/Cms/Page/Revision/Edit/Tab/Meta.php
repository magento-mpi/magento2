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
 * Meta tab with cms page attributes and some modifications to CE version
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Meta
    extends Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Meta
{
    /**
     * Cms data
     *
     * @var Magento_VersionsCms_Helper_Data
     */
    protected $_cmsData = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_VersionsCms_Helper_Data $cmsData
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_VersionsCms_Helper_Data $cmsData,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($coreRegistry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Adding onchange js call
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Meta
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $this->_cmsData->addOnChangeToFormElements($this->getForm(), 'dataChanged();');

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
