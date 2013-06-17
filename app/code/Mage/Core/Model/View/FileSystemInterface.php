<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


interface Mage_Core_Model_View_FileSystemInterface
{
    /**
     * Scope separator
     */
    const SCOPE_SEPARATOR = '::';

    /**
     * Path to configuration node that indicates how to materialize view files: with or without "duplication"
     */
    const XML_PATH_ALLOW_DUPLICATION = 'global/design/theme/allow_view_files_duplication';

    /**
     * XPath for configuration setting of signing static files
     */
    const XML_PATH_STATIC_FILE_SIGNATURE = 'dev/static/sign';

    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_MODULE_DIR = '_module';
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';

    /**
     * Get existing file name with fallback to default
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    //public function getFilename($file, array $params = array());

    /**
     * Get a locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    //public function getLocaleFileName($file, array $params = array());

    /**
     * Find a view file using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    //public function getViewFile($file, array $params = array());

    /**
     * Get url to file base on theme file identifier.
     * Publishes file there, if needed.
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    //public function getViewFileUrl($file, array $params = array());

    /**
     * Publish file (if needed) and return its public path
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    //public function getViewFilePublicPath($file, array $params = array());

    /**
     * Get url to public file
     *
     * @param string $file
     * @param bool|null $isSecure
     * @return string
     * @throws Magento_Exception
     */
    //public function getPublicFileUrl($file, $isSecure = null);

    /**
     * Publish relative $fileUrl based on information about parent file path and name.
     *
     * The method is public only because PHP 5.3 does not permit usage of protected methods inside the closures,
     * even if a closure is created in the same class. The method is not intended to be used by a client of this class.
     * If you ever need to call this method externally, then ensure you have a good reason for it. As such the method
     * would need to be added to the class's interface and proxy.
     *
     * @param string $fileUrl URL to the file that was extracted from $parentFilePath
     * @param string $parentFilePath path to the file
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    //public function publishRelatedViewFile($fileUrl, $parentFilePath, $parentFileName, $params);

    /**
     * Return directory for theme files publication
     *
     * @return string
     */
    //public function getPublicDir();
}
