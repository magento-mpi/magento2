<?php
/**
 * Locale inheritance hierarchy loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Locale_Hierarchy_Loader
{
    const XML_PATH_LOCALE_INHERITANCE = 'global/locale/inheritance';

    /**
     * @var Magento_Core_Model_Config_Locales
     */
    protected $_config;

    /**
     * Locales configuration model
     *
     * @param Magento_Core_Model_Config_Locales $config
     */
    public function __construct(Magento_Core_Model_Config_Locales $config)
    {
        $this->_config = $config;
    }

    /**
     * Compose locale inheritance hierarchy based on given config
     *
     * @param array|string $localeConfig assoc array where key is a code of locale and value is a code of its parent locale
     * @return array
     */
    protected function _composeLocaleHierarchy($localeConfig)
    {
        $localeHierarchy = array();
        if (!is_array($localeConfig)) {
            return $localeHierarchy;
        }

        foreach ($localeConfig as $locale => $localeParent) {
            $localeParents = array($localeParent);
            while (isset($localeConfig[$localeParent]) && !in_array($localeConfig[$localeParent], $localeParents)
                && $locale != $localeConfig[$localeParent]
            ) {
                // inheritance chain starts with the deepest parent
                array_unshift($localeParents, $localeConfig[$localeParent]);
                $localeParent = $localeConfig[$localeParent];
            }
            // store hierarchy for current locale
            $localeHierarchy[$locale] = $localeParents;
        }
        return $localeHierarchy;
    }

    /**
     * Load locales inheritance hierarchy
     *
     * @return array
     */
    public function load()
    {
        $localeHierarchy = array();
        $inheritanceNode = $this->_config->getNode(self::XML_PATH_LOCALE_INHERITANCE);
        if ($inheritanceNode instanceof Magento_Simplexml_Element) {
            $localeHierarchy = $this->_composeLocaleHierarchy(
                $inheritanceNode->asCanonicalArray()
            );
        }
        return $localeHierarchy;
    }
}
