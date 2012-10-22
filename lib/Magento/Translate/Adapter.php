<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Translate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento translate adapter
 */
class Magento_Translate_Adapter extends Magento_Translate_AdapterAbstract
{
    /**
     * Translate message string.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array|string $messageId
     * @param null $locale
     * @return string
     */
    public function translate($messageId, $locale = null)
    {
        $translator = $this->getOptions('translator');
        $method = $this->getOptions('translate_method');
        if (is_callable(array($translator, $method))) {
            return call_user_func(array($translator, $method), $messageId);
        } else {
            return $messageId;
        }
    }

    /**
     * Translate message string.
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @param string $messageId
     * @return string
     */
    public function __($messageId)
    {
        return $this->translate($messageId);
    }
}
