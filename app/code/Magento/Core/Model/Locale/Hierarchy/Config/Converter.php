<?php
/**
 * Locale hierarchy configuration converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Locale_Hierarchy_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Compose locale inheritance hierarchy based on given config
     *
     * @param array $localeConfig assoc array where key is a code of locale and value is a code of its parent locale
     * @return array
     */
    protected function _composeLocaleHierarchy($localeConfig)
    {
        $localeHierarchy = array();
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
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        /** @var DOMNodeList $locales */
        $locales = $source->getElementsByTagName('locale');
        /** @var DOMNode $locale */
        foreach ($locales as $locale) {
            $parent = $locale->attributes->getNamedItem('parent');
            if ($parent) {
                $output[$locale->attributes->getNamedItem('code')->nodeValue] = $parent->nodeValue;
            }

        }
        return $this->_composeLocaleHierarchy($output);
    }
}
