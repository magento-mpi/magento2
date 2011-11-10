<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme configuration files handler
 */
class Magento_Config_Theme extends Magento_Config_XmlAbstract
{
    /**
     * Get absolute path to theme.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/theme.xsd';
    }

    /**
     * Get title for specified package code
     *
     * @param string $code
     * @return string|false
     */
    public function getPackageTitle($code)
    {
        return $this->_getScalarNodeValue("/design/package[@code='{$code}']/title");
    }

    /**
     * Get title for specified theme and package code
     *
     * @param string $themeCode
     * @param string $packageCode
     * @return string|false
     */
    public function getThemeTitle($themeCode, $packageCode)
    {
        return $this->_getScalarNodeValue("/design/package[@code='{$packageCode}']/theme[@code='{$themeCode}']/title");
    }

    /**
     * Treat provided xPath query as a reference to fully qualified element with scalar value
     *
     * @param string $xPathQuery
     * @return string|false
     */
    protected function _getScalarNodeValue($xPathQuery)
    {
        $xPath = new DOMXPath($this->_dom);
        /** @var DOMElement $element */
        foreach ($xPath->query($xPathQuery) as $element) {
            return (string)$element->nodeValue;
        }
        return false;
    }

    /**
     * Getter for Magento versions compatible with theme
     *
     * return an array with 'from' and 'to' keys
     *
     * @param string $package
     * @param string $theme
     * @throw Exception an exception in case of unknown theme
     * @return array
     */
    public function getCompatibleVersions($package, $theme)
    {
        $xPath = new DOMXPath($this->_dom);
        $version = $xPath
            ->query("/design/package[@code='{$package}']/theme[@code='{$theme}']/requirements/magento_version")
            ->item(0);
        if (!$version) {
            throw new Exception('Unknown theme "' . $theme . '" in "' . $package . '" package.');
        }
        $result = array(
            'from'  => $version->getAttribute('from'),
            'to'    => $version->getAttribute('to')
        );
        return $result;
    }

    /**
     * Get initial XML of a valid document
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><design></design>';
    }

    /**
     * Design packages are unique by code. Themes are unique by code.
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array('/design/package' => 'code', '/design/package/theme' => 'code');
    }
}
