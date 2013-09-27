<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset renderer which expanded by default
 */
class Magento_Paypal_Block_Adminhtml_System_Config_Fieldset_Expanded
    extends Magento_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Backend_Model_Auth_Session $authSession,
        array $data = array()
    ) {
        parent::__construct($context, $authSession, $data);
    }

    /**
     * Return collapse state
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        $extra = $this->_authSession->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }
}
