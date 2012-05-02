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
     * @param string $package
     * @param string $theme
     * @return string|false
     */
    public function getThemeTitle($package, $theme)
    {
        return $this->_getScalarNodeValue("/design/package[@code='{$package}']/theme[@code='{$theme}']/title");
    }

    /**
     * Retrieve a parent theme code
     *
     * @param string $package
     * @param string $theme
     * @return string|null
     */
    public function getParentTheme($package, $theme)
    {
        $this->_ensureThemeExists($package, $theme);
        $xPath = new DOMXPath($this->_dom);
        /** @var $themeNode DOMElement */
        $themeNode = $xPath->query("/design/package[@code='{$package}']/theme[@code='{$theme}']")->item(0);
        return $themeNode->getAttribute('parent') ?: null;
    }

    /**
     * Check whether a theme exists in a design package
     *
     * @param string $package
     * @param string $theme
     * @throws Magento_Exception
     */
    protected function _ensureThemeExists($package, $theme)
    {
        $xPath = new DOMXPath($this->_dom);
        $themeNodeList = $xPath->query("/design/package[@code='{$package}']/theme[@code='{$theme}']");
        if (!$themeNodeList->length) {
            throw new Magento_Exception('Unknown theme "' . $theme . '" in "' . $package . '" package.');
        }
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
     * @return array
     */
    public function getCompatibleVersions($package, $theme)
    {
        $this->_ensureThemeExists($package, $theme);
        $xPath = new DOMXPath($this->_dom);
        /** @var $version DOMElement */
        $version = $xPath
            ->query("/design/package[@code='{$package}']/theme[@code='{$theme}']/requirements/magento_version")
            ->item(0);
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
