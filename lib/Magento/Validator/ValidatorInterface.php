<?php
/**
 * Validator interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Validator;

interface ValidatorInterface extends \Zend_Validate_Interface
{
    /**
     * Set translator instance.
     *
     * @param \Magento\Framework\Translate\AdapterInterface|null $translator
     * @return \Magento\Validator\ValidatorInterface
     */
    public function setTranslator($translator = null);

    /**
     * Get translator.
     *
     * @return \Magento\Framework\Translate\AdapterInterface|null
     */
    public function getTranslator();

    /**
     * Check that translator is set.
     *
     * @return boolean
     */
    public function hasTranslator();
}
