<?php
/**
 * Google AdWords Color Backend model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleAdwords_Model_Config_Backend_Color extends Magento_GoogleAdwords_Model_Config_Backend_ConversionAbstract
{
    /**
     * Validation rule conversion color
     *
     * @return Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        $this->_validatorComposite->addRule($this->_validatorFactory->createColorValidator($this->getValue()),
            'conversion_color');
        return $this->_validatorComposite;
    }

    /**
     * Get tested value
     *
     * @return string
     */
    public function getConversionColor()
    {
        return $this->getValue();
    }
}
