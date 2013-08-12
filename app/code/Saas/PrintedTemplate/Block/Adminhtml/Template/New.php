<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template edit block
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_New extends Magento_Backend_Block_Widget
{
    /**
     * Set template
     */
    public function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::new.phtml');
    }

    /**
     * Prepares buttons
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                        'class'   => 'back'
                    )
                )
        );
        $this->setChild('reset_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Reset'),
                        'onclick' => 'window.location.href = window.location.href'
                    )
                )
        );
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'     => $this->__('Continue'),
                        'onclick'   => "setSettings('".$this->getContinueUrl()."','entity_type')",
                        'class'     => 'save',
                    )
                )
        );

        return parent::_prepareLayout();
    }

    /**
     * Html for button "Back"
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Html for button "Reset"
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Html for button "Continue"
     *
     * @return string
     */
    public function getContinueButtonHtml()
    {
        return $this->getChildHtml('continue_button');
    }

    /**
     * Returns available template types
     *
     * @return array
     */
    public function getAllTypes()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Source_Type')->getAllOptions();
    }

    /**
     * Retunrs url to continue
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/edit', array(
            '_current'  => true,
            'entity_type'      => '{{entity_type}}'
        ));
    }

    /**
     * Prepares header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('New Printed Template');
    }
}
