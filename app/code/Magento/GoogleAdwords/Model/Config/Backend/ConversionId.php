<?php
/**
 * Google AdWords Conversion Id Backend model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleAdwords\Model\Config\Backend;

class ConversionId
    extends \Magento\GoogleAdwords\Model\Config\Backend\ConversionAbstract
{
    /**
     * Validation rule conversion id
     *
     * @return \Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        $this->_validatorComposite->addRule($this->_validatorFactory->createConversionIdValidator($this->getValue()),
            'conversion_id');
        return $this->_validatorComposite;
    }

    /**
     * Get tested value
     *
     * @return string
     */
    public function getConversionId()
    {
        return $this->getValue();
    }
}
