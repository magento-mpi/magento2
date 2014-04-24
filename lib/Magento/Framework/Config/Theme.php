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
namespace Magento\Framework\Config;

class Theme
{
    /**
     * Is used for separation path of themes
     */
    const THEME_PATH_SEPARATOR = '/';

    /**
     * Data extracted from the configuration file
     *
     * @var array
     */
    protected $_data;

    /**
     * Constructor
     *
     * @param string $configContent
     */
    public function __construct($configContent)
    {
        $config = new \DOMDocument();
        $config->loadXML($configContent);
        // todo: validation of the document
        $this->_data = $this->_extractData($config);
    }

    /**
     * Get absolute path to theme.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/etc/theme.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param \DOMDocument $dom
     * @return array
     */
    protected function _extractData(\DOMDocument $dom)
    {
        /** @var $themeNode \DOMElement */
        $themeNode = $dom->getElementsByTagName('theme')->item(0);
        /** @var $mediaNode \DOMElement */
        $mediaNode = $themeNode->getElementsByTagName('media')->item(0);

        $themeVersionNode = $themeNode->getElementsByTagName('version')->item(0);
        $themeParentNode = $themeNode->getElementsByTagName('parent')->item(0);
        $themeTitleNode = $themeNode->getElementsByTagName('title')->item(0);
        $previewImage = $mediaNode ? $mediaNode->getElementsByTagName('preview_image')->item(0)->nodeValue : '';

        return array(
            'title' => $themeTitleNode->nodeValue,
            'parent' => $themeParentNode ? $themeParentNode->nodeValue : null,
            'version' => $themeVersionNode ? $themeVersionNode->nodeValue : null,
            'media' => array('preview_image' => $previewImage)
        );
    }

    /**
     * Get title for specified package code
     *
     * @return string
     */
    public function getThemeVersion()
    {
        return $this->_data['version'];
    }

    /**
     * Get title for specified theme and package code
     *
     * @return string
     */
    public function getThemeTitle()
    {
        return $this->_data['title'];
    }

    /**
     * Get theme media data
     *
     * @return array
     */
    public function getMedia()
    {
        return $this->_data['media'];
    }

    /**
     * Retrieve a parent theme code
     *
     * @return array|null
     */
    public function getParentTheme()
    {
        $parentTheme = $this->_data['parent'];
        if (!$parentTheme) {
            return null;
        }
        return explode(self::THEME_PATH_SEPARATOR, $parentTheme);
    }
}
