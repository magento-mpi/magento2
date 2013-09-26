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
 * Tool block with buttons
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Preview_Buttons extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * @var Magento_VersionsCms_Model_Config
     */
    protected $_cmsConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_VersionsCms_Model_Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_VersionsCms_Model_Config $cmsConfig,
        array $data = array()
    ) {
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding two main buttons
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Preview_Buttons
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('preview', array(
                'id' => 'preview-buttons-preview',
                'label' => 'Preview',
                'class' => 'preview',
                'onclick' => 'preview()'
            ));

        if ($this->_cmsConfig->canCurrentUserPublishRevision()) {
            $this->_addButton('publish', array(
                'id' => 'preview-buttons-publish',
                'label' => 'Publish',
                'class' => 'publish',
                'onclick' => 'publish()'
            ));
        }
    }

    /**
     * Override parent method to produce only button's html in result
     *
     * @return string
     */
    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getButtonsHtml();
    }
}
