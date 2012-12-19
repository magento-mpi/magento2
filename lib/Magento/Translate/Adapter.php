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
     * @param null|string $locale
     * @return string
     */
    public function translate($messageId, $locale = null)
    {
        $translator = $this->getOptions('translator');
        if (is_callable($translator)) {
            return call_user_func($translator, $messageId);
        } else {
            return $messageId;
        }
    }

    /**
     * Translate message string.
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $messageId = array_shift($args);
        $string = $this->translate($messageId);
        if (count($args) > 0) {
            $string = vsprintf($string, $args);
        }
        return $string;
    }
}
