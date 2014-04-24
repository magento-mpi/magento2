<?php
/**
 * Abstract validator class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Validator;

abstract class AbstractValidator implements \Magento\Framework\Validator\ValidatorInterface
{
    /**
     * @var \Magento\Framework\Translate\AdapterInterface|null
     */
    protected static $_defaultTranslator = null;

    /**
     * @var \Magento\Framework\Translate\AdapterInterface|null
     */
    protected $_translator = null;

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Set default translator instance
     *
     * @param \Magento\Framework\Translate\AdapterInterface|null $translator
     * @return void
     */
    public static function setDefaultTranslator(\Magento\Framework\Translate\AdapterInterface $translator = null)
    {
        self::$_defaultTranslator = $translator;
    }

    /**
     * Get default translator
     *
     * @return \Magento\Framework\Translate\AdapterInterface|null
     */
    public static function getDefaultTranslator()
    {
        return self::$_defaultTranslator;
    }

    /**
     * Set translator instance
     *
     * @param \Magento\Framework\Translate\AdapterInterface|null $translator
     * @return \Magento\Framework\Validator\AbstractValidator
     */
    public function setTranslator($translator = null)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Get translator
     *
     * @return \Magento\Framework\Translate\AdapterInterface|null
     */
    public function getTranslator()
    {
        if (is_null($this->_translator)) {
            return self::getDefaultTranslator();
        }
        return $this->_translator;
    }

    /**
     * Check that translator is set.
     *
     * @return boolean
     */
    public function hasTranslator()
    {
        return !is_null($this->_translator);
    }

    /**
     * Get validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Clear messages
     *
     * @return void
     */
    protected function _clearMessages()
    {
        $this->_messages = array();
    }

    /**
     * Add messages
     *
     * @param array $messages
     * @return void
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages = array_merge_recursive($this->_messages, $messages);
    }
}
