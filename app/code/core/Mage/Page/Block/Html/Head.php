<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Head extends Mage_Core_Block_Template
{
    /**
     * Initialize template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('page/html/head.phtml');
    }

    /**
     * Add CSS file to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addCss($name, $params = "")
    {
        $this->addItem('skin_css', $name, $params);
        return $this;
    }

    /**
     * Add JavaScript file to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addJs($name, $params = "")
    {
        $this->addItem('js', $name, $params);
        return $this;
    }

    /**
     * Add CSS file for Internet Explorer only to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addCssIe($name, $params = "")
    {
        $this->addItem('skin_css', $name, $params, 'IE');
        return $this;
    }

    /**
     * Add JavaScript file for Internet Explorer only to HEAD entity
     *
     * @param string $name
     * @param string $params
     * @return Mage_Page_Block_Html_Head
     */
    public function addJsIe($name, $params = "")
    {
        $this->addItem('js', $name, $params, 'IE');
        return $this;
    }

    /**
     * Add Link element to HEAD entity
     *
     * @param string $rel forward link types
     * @param string $href URI for linked resource
     * @return Mage_Page_Block_Html_Head
     */
    public function addLinkRel($rel, $href)
    {
        $this->addItem('link_rel', $href, 'rel="' . $rel . '"');
        return $this;
    }

    /**
     * Add HEAD Item
     *
     * Allowed types:
     *  - js
     *  - js_css
     *  - skin_js
     *  - skin_css
     *  - rss
     *
     * @param string $type
     * @param string $name
     * @param string $params
     * @param string $if
     * @param string $cond
     * @return Mage_Page_Block_Html_Head
     */
    public function addItem($type, $name, $params=null, $if=null, $cond=null)
    {
        if (empty($name)) {
            throw new Exception('File name must be not empty.');
        }
        if (empty($params) && in_array($type, array('skin_css', 'js_css'))) {
            $params = 'media="all"';
        }
        $this->_data['items'][$type.'/'.$name] = array(
            'type'   => $type,
            'name'   => $name,
            'params' => $params,
            'if'     => $if,
            'cond'   => $cond,
       );
        return $this;
    }

    /**
     * Remove Item from HEAD entity
     *
     * @param string $type
     * @param string $name
     * @return Mage_Page_Block_Html_Head
     */
    public function removeItem($type, $name)
    {
        unset($this->_data['items'][$type.'/'.$name]);
        return $this;
    }

    public function getCssJsHtml()
    {
        $lines = array();
        $meta  = array();
        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond'])) {
                continue;
            }
            $contentType = $this->_mapToContentType($item['type']);
            if ($contentType == 'css') {
                $item['params'] .= ' rel="stylesheet" type="text/css"';
            } elseif ($item['type'] == 'rss') {
                $item['params'] .= ' rel="alternate" type="application/rss+xml"';
            }
            $group = $item['if'] . '|' . (empty($item['params']) ? '_' : $item['params']) . '|' . $contentType;
            $meta[$group] = array($item['if'], (string)$item['params'], $contentType);
            $lines[$group][$item['name']] = $this->_mapToStaticType($item['type']);
        }

        $html   = '';
        foreach ($lines as $group => $items) {
            list($if, $params, $contentType) = $meta[$group];
            if (!empty($if)) {
                $html .= '<!--[if ' . $if .']>'."\n";
            }
            if ($params) {
                $params = ' ' . trim($params);
            }
            switch ($contentType) {
                case 'css':
                    foreach (Mage::getDesign()->getOptimalCssUrls($items) as $url) {
                        $html .= sprintf('<link%s href="%s" />' . "\n", $params, $url);
                    }
                break;
                case 'js':
                    foreach (Mage::getDesign()->getOptimalJsUrls($items) as $url) {
                        $html .= sprintf('<script%s type="text/javascript" src="%s"></script>' . "\n", $params, $url);
                    }
                break;
                case 'link':
                    foreach ($items as $file => $type) {
                        $html .= sprintf('<link%s href="%s" />' . "\n", $params, $file);
                    }
                break;
            }
            if (!empty($if)) {
                $html .= '<![endif]-->' . "\n";
            }
        }
        return $html;
    }

    /**
     * Map block types to design model static types
     *
     * @param string $type
     * @return string
     */
    protected function _mapToStaticType($type)
    {
        switch ($type) {
            case 'js_css':
            case 'js':
                return Mage_Core_Model_Design_Package::STATIC_TYPE_LIB;
            case 'skin_css':
            case 'skin_js':
                return Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN;
            default:
                return '';
        }
    }

    /**
     * Map block types to content types
     *
     * @param string $type
     * @return string
     * @throws Exception on unsupported type
     */
    protected function _mapToContentType($type)
    {
        switch ($type) {
            case 'js_css':
            case 'skin_css':
                return 'css';
            case 'js':
            case 'skin_js':
                return 'js';
            case 'rss':
            case 'link_rel':
                return 'link';
            default:
                throw new Exception("Unknown type: '{$type}'.");
        }
    }

    /**
     * Retrieve Content Type
     *
     * @return string
     */
    public function getContentType()
    {
        if (empty($this->_data['content_type'])) {
            $this->_data['content_type'] = $this->getMediaType().'; charset='.$this->getCharset();
        }
        return $this->_data['content_type'];
    }

    /**
     * Retrieve Media Type
     *
     * @return string
     */
    public function getMediaType()
    {
        if (empty($this->_data['media_type'])) {
            $this->_data['media_type'] = Mage::getStoreConfig('design/head/default_media_type');
        }
        return $this->_data['media_type'];
    }

    /**
     * Retrieve Charset
     *
     * @return string
     */
    public function getCharset()
    {
        if (empty($this->_data['charset'])) {
            $this->_data['charset'] = Mage::getStoreConfig('design/head/default_charset');
        }
        return $this->_data['charset'];
    }

    /**
     * Set title element text
     *
     * @param string $title
     * @return Mage_Page_Block_Html_Head
     */
    public function setTitle($title)
    {
        $this->_data['title'] = Mage::getStoreConfig('design/head/title_prefix') . ' ' . $title
            . ' ' . Mage::getStoreConfig('design/head/title_suffix');
        return $this;
    }

    /**
     * Retrieve title element text (encoded)
     *
     * @return string
     */
    public function getTitle()
    {
        if (empty($this->_data['title'])) {
            $this->_data['title'] = $this->getDefaultTitle();
        }
        return htmlspecialchars(html_entity_decode(trim($this->_data['title']), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Retrieve default title text
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return Mage::getStoreConfig('design/head/default_title');
    }

    /**
     * Retrieve content for description tag
     *
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->_data['description'])) {
            $this->_data['description'] = Mage::getStoreConfig('design/head/default_description');
        }
        return $this->_data['description'];
    }

    /**
     * Retrieve content for keyvords tag
     *
     * @return string
     */
    public function getKeywords()
    {
        if (empty($this->_data['keywords'])) {
            $this->_data['keywords'] = Mage::getStoreConfig('design/head/default_keywords');
        }
        return $this->_data['keywords'];
    }

    /**
     * Retrieve URL to robots file
     *
     * @return string
     */
    public function getRobots()
    {
        if (empty($this->_data['robots'])) {
            $this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        }
        return $this->_data['robots'];
    }

    /**
     * Get miscellanious scripts/styles to be included in head before head closing tag
     *
     * @return string
     */
    public function getIncludes()
    {
        if (empty($this->_data['includes'])) {
            $this->_data['includes'] = Mage::getStoreConfig('design/head/includes');
        }
        return $this->_data['includes'];
    }

    /**
     * Getter for path to Favicon
     *
     * @return string
     */
    public function getFaviconFile()
    {
        if (empty($this->_data['favicon_file'])) {
            $this->_data['favicon_file'] = $this->_getFaviconFile();
        }
        return $this->_data['favicon_file'];
    }

    /**
     * Retrieve path to Favicon
     *
     * @return string
     */
    protected function _getFaviconFile()
    {
        $folderName = Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon::UPLOAD_DIR;
        $storeConfig = Mage::getStoreConfig('design/head/shortcut_icon');
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if(!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            $url = $faviconFile;
        } else {
            $url = $this->getSkinUrl('favicon.ico');
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename
     * @return bool
     */
    protected function _isFile($filename) {
        if (Mage::helper('core/file_storage_database')->checkDbUsage() && !is_file($filename)) {
            Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
        return is_file($filename);
    }
}
