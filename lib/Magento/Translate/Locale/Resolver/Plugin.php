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
namespace Magento\Translate\Locale\Resolver;

class Plugin
{
    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translate;

    /**
     * @param \Magento\TranslateInterface $translate
     */
    function __construct(\Magento\TranslateInterface $translate)
    {
        $this->_translate = $translate;
    }

    /**
     * @param string|null $localeCode
     */
    public function afterEmulate($localeCode)
    {
        $this->_init($localeCode);
    }

    /**
     * @param string|null $localeCode
     */
    public function afterRevert($localeCode)
    {
        $this->_init($localeCode);
    }

    /**
     * @param string|null $localeCode
     */
    protected function _init($localeCode)
    {
        if (!is_null($localeCode)) {
            $this->_translate->initLocale($localeCode);
        }
    }
}
