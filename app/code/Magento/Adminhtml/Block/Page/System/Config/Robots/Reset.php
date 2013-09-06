<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Reset to Defaults" button renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Page_System_Config_Robots_Reset extends Magento_Backend_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('page/system/config/robots/reset.phtml');
    }

    /**
     * Get robots.txt custom instruction default value
     *
     * @return string
     */
    public function getRobotsDefaultCustomInstructions()
    {
        return Mage::helper('Magento_Page_Helper_Robots')->getRobotsDefaultCustomInstructions();
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'id'      => 'reset_to_default_button',
                'label'   => __('Reset to Default'),
                'onclick' => 'javascript:resetRobotsToDefault(); return false;'
            ));

        return $button->toHtml();
    }

    /**
     * Render button
     *
     * @param  Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  Magento_Data_Form_Element_Abstract $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
}
