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
 * Magento translate abstract adapter
 */
namespace Magento\Translate;

abstract class AbstractAdapter extends \Zend_Translate_Adapter
    implements AdapterInterface
{
    /**
     * Load translation data
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $data
     * @param string|\Zend_Locale $locale
     * @param array $options (optional)
     * @return array
     */
    protected function _loadTranslationData($data, $locale, array $options = array())
    {
        return array();
    }

    /**
     * Is translation available.
     *
     * Return false, as \Zend_Validate pass message into translator only when isTranslated is false
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param string $messageId
     * @param bool $original
     * @param null $locale
     * @return bool
     */
    public function isTranslated($messageId, $original = false, $locale = null)
    {
        return false;
    }

    /**
     * Stub for setLocale functionality
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param string|\Zend_Locale $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        return $this;
    }


    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Magento\Translate\Adapter';
    }
}
