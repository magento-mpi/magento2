<?php
/**
 * Validator interface
 *
 * @copyright {}
 */
interface Magento_Validator_ValidatorInterface extends Zend_Validate_Interface
{
    /**
     * Set translator instance.
     *
     * @param Magento_Translate_AdapterInterface|null $translator
     * @return Magento_Validator_ValidatorInterface
     */
    public function setTranslator($translator = null);

    /**
     * Get translator.
     *
     * @return Magento_Translate_AdapterInterface|null
     */
    public function getTranslator();

    /**
     * Check that translator is set.
     *
     * @return boolean
     */
    public function hasTranslator();
}
