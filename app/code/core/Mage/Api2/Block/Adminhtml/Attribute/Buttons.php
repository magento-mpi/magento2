<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Api2
 */

/**
 * Block for rendering buttons
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Attribute_Buttons extends Mage_Adminhtml_Block_Template
{
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('api2/attribute/buttons.phtml');
    }

    /**
     * Prepare global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $buttons = array(
            'backButton'    => array(
                'label'     => $this->__('Back'),
                'onclick'   => sprintf("window.location.href='%s';", $this->getUrl('*/*/')),
                'class'     => 'back'
            ),
            'saveButton'    => array(
                'label'     => $this->__('Save'),
                'onclick'   => 'form.submit(); return false;',
                'class'     => 'save'
            ),
        );

        foreach ($buttons as $name => $data) {
            $button = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($data);
            $this->setChild($name, $button);
        }

        return parent::_prepareLayout();
    }

    /**
     * Get back button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    /**
     * Get reset button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    /**
     * Get save button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    /**
     * Get block caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->__('Edit');
    }
}
