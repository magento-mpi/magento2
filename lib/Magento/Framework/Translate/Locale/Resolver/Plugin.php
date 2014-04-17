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
namespace Magento\Framework\Translate\Locale\Resolver;

/**
 * Magento translate abstract adapter
 */
class Plugin
{
    /**
     * @var \Magento\Framework\TranslateInterface
     */
    protected $_translate;

    /**
     * @param \Magento\Framework\TranslateInterface $translate
     */
    public function __construct(\Magento\Framework\TranslateInterface $translate)
    {
        $this->_translate = $translate;
    }

    /**
     * @param \Magento\Locale\ResolverInterface $subject
     * @param string|null $localeCode
     * @return void
     */
    public function afterEmulate(\Magento\Locale\ResolverInterface $subject, $localeCode)
    {
        $this->_init($localeCode);
    }

    /**
     * @param \Magento\Locale\ResolverInterface $subject
     * @param string|null $localeCode
     * @return void
     */
    public function afterRevert(\Magento\Locale\ResolverInterface $subject, $localeCode)
    {
        $this->_init($localeCode);
    }

    /**
     * @param string|null $localeCode
     * @return void
     */
    protected function _init($localeCode)
    {
        if (!is_null($localeCode)) {
            $this->_translate->setLocale($localeCode)
                ->loadData(null, true);
        }
    }
}
