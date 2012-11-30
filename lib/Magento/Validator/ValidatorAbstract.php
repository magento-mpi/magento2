<?php
/**
 * Abstract validator class.
 *
 * @copyright {}
 */
abstract class Magento_Validator_ValidatorAbstract implements Magento_Validator_ValidatorInterface
{
    /**
     * @var Magento_Translate_AdapterInterface|null
     */
    protected static $_defaultTranslator = null;

    /**
     * @var Magento_Translate_AdapterInterface|null
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
     * @param Magento_Translate_AdapterInterface|null $translator
     */
    public static function setDefaultTranslator(Magento_Translate_AdapterInterface $translator = null)
    {
        self::$_defaultTranslator = $translator;
    }

    /**
     * Get default translator
     *
     * @return Magento_Translate_AdapterInterface|null
     */
    public static function getDefaultTranslator()
    {
        return self::$_defaultTranslator;
    }

    /**
     * Set translator instance
     *
     * @param Magento_Translate_AdapterInterface|null $translator
     * @return Magento_Validator_ValidatorAbstract
     */
    public function setTranslator($translator = null)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Get translator
     *
     * @return Magento_Translate_AdapterInterface|null
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
     */
    protected function _clearMessages()
    {
        $this->_messages = array();
    }

    /**
     * Add messages
     *
     * @param array $messages
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages = array_merge_recursive($this->_messages, $messages);
    }
}
