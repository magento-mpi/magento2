<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Design_PackageInterface
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Scope separator
     */
    const SCOPE_SEPARATOR = '::';

    /**#@+
     * Extensions group for static files
     */
    const CONTENT_TYPE_CSS = 'css';
    const CONTENT_TYPE_JS  = 'js';
    /**#@-*/

    /**#@+
     * Protected extensions group for publication mechanism
     */
    const CONTENT_TYPE_PHP   = 'php';
    const CONTENT_TYPE_PHTML = 'phtml';
    const CONTENT_TYPE_XML   = 'xml';
    /**#@-*/

    /**
     * Set package area
     *
     * @param string $area
     * @return Mage_Core_Model_Design_PackageInterface
     */
    public function setArea($area);

    /**
     * Retrieve package area
     *
     * @return string
     */
    public function getArea();

    /**
     * Set theme path
     *
     * @param Mage_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Mage_Core_Model_Design_PackageInterface
     */
    public function setDesignTheme($theme, $area = null);

    /**
     * Get default theme which declared in configuration
     *
     * @param string $area
     * @param array $params
     * @return string|int
     */
    public function getConfigurationDesignTheme($area = null, array $params = array());

    /**
     * Set default design theme
     *
     * @return Mage_Core_Model_Design_PackageInterface
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return Mage_Core_Model_Theme
     */
    public function getDesignTheme();

    /**
     * Get existing file name with fallback to default
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params = array());

    /**
     * Get a locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFileName($file, array $params = array());

    /**
     * Find a view file using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFile($file, array $params = array());

    /**
     * Publish file (if needed) and return its public path
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFilePublicPath($file, array $params = array());


    /**
     * Get url to file base on theme file identifier.
     * Publishes file there, if needed.
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($file, array $params = array());

    /**
     * Get url to public file
     *
     * @param string $file
     * @param bool|null $isSecure
     * @return string
     * @throws Magento_Exception
     */
    public function getPublicFileUrl($file, $isSecure = null);

    /**
     * Return directory for theme files publication
     *
     * @return string
     */
    public function getPublicDir();

    /**
     * Render view config object for current package and theme
     *
     * @return Magento_Config_View
     */
    public function getViewConfig();
}
