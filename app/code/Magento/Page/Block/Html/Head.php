<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Html page block
 *
 * @category   Magento
 * @package    Magento_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Page\Block\Html;

class Head extends \Magento\View\Element\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'html/head.phtml';

    /**
     * Chunks of title (necessary for backend)
     *
     * @var array
     */
    protected $_titleChunks;

    /**
     * Page title without prefix and suffix when not chunked
     *
     * @var string
     */
    protected $_pureTitle;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\View\Asset\MergeService
     */
    private $_assetMergeService;

    /**
     * @var \Magento\View\Asset\MinifyService
     */
    private $_assetMinifyService;

    /**
     * @var \Magento\View\Asset\GroupedCollection
     */
    private $_pageAssets;

    /**
     * Core file storage database
     *
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_fileStorageDatabase = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\View\Asset\GroupedCollection $assets
     * @param \Magento\View\Asset\MergeService $assetMergeService
     * @param \Magento\View\Asset\MinifyService $assetMinifyService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\ObjectManager $objectManager,
        \Magento\View\Asset\GroupedCollection $assets,
        \Magento\View\Asset\MergeService $assetMergeService,
        \Magento\View\Asset\MinifyService $assetMinifyService,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->_fileStorageDatabase = $fileStorageDatabase;
        $this->_objectManager = $objectManager;
        $this->_assetMergeService = $assetMergeService;
        $this->_assetMinifyService = $assetMinifyService;
        $this->_pageAssets = $assets;
    }

    /**
     * Add RSS element to HEAD entity
     *
     * @param string $title
     * @param string $href
     * @return \Magento\Page\Block\Html\Head
     */
    public function addRss($title, $href)
    {
        $attributes = 'rel="alternate" type="application/rss+xml" title="' . $title . '"';
        $asset = $this->_objectManager->create(
            'Magento\View\Asset\Remote', array('url' => (string)$href)
        );
        $this->_pageAssets->add("link/$href", $asset, array('attributes' => $attributes));
        return $this;
    }

    /**
     * Render HTML for the added head items
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $block) {
            /** @var $block \Magento\View\Element\AbstractBlock */
            if ($block instanceof \Magento\Theme\Block\Html\Head\AssetBlockInterface) {
                /** @var \Magento\View\Asset\AssetInterface $asset */
                $asset = $block->getAsset();
                $this->_pageAssets->add(
                    $block->getNameInLayout(),
                    $asset,
                    (array)$block->getProperties()
                );
            }
        }

        $result = '';
        /** @var $group \Magento\View\Asset\PropertyGroup */
        foreach ($this->_pageAssets->getGroups() as $group) {
            $contentType = $group->getProperty(\Magento\View\Asset\GroupedCollection::PROPERTY_CONTENT_TYPE);
            $canMerge = $group->getProperty(\Magento\View\Asset\GroupedCollection::PROPERTY_CAN_MERGE);
            $attributes = $group->getProperty('attributes');
            $ieCondition = $group->getProperty('ie_condition');
            $flagName = $group->getProperty('flag_name');

            if ($flagName && !$this->getData($flagName)) {
                continue;
            }

            $groupAssets = $group->getAll();
            $groupAssets = $this->_assetMinifyService->getAssets($groupAssets);
            if ($canMerge && count($groupAssets) > 1) {
                $groupAssets = $this->_assetMergeService->getMergedAssets($groupAssets, $contentType);
            }

            if (!empty($attributes)) {
                if (is_array($attributes)) {
                    $attributesString = '';
                    foreach ($attributes as $name => $value) {
                        $attributesString .= ' ' . $name . '="' . $this->escapeHtml($value) . '"';
                    }
                    $attributes = $attributesString;
                } else {
                    $attributes = ' ' . $attributes;
                }
            }

            if ($contentType == \Magento\View\Publisher::CONTENT_TYPE_JS ) {
                $groupTemplate = '<script' . $attributes . ' type="text/javascript" src="%s"></script>' . "\n";
            } else {
                if ($contentType == \Magento\View\Publisher::CONTENT_TYPE_CSS) {
                    $attributes = ' rel="stylesheet" type="text/css"' . ($attributes ?: ' media="all"');
                }
                $groupTemplate = '<link' . $attributes . ' href="%s" />' . "\n";
            }

            $groupHtml = $this->_renderHtml($groupTemplate, $groupAssets);

            if (!empty($ieCondition)) {
                $groupHtml = '<!--[if ' . $ieCondition . ']>' . "\n" . $groupHtml . '<![endif]-->' . "\n";
            }

            $result .= $groupHtml;
        }
        return $result;
    }

    /**
     * Render HTML tags referencing corresponding URLs
     *
     * @param string $template
     * @param array|Iterator $assets
     * @return string
     */
    protected function _renderHtml($template, $assets)
    {
        $result = '';
        try {
            /** @var $asset \Magento\View\Asset\AssetInterface */
            foreach ($assets as $asset) {
                $result .= sprintf($template, $asset->getUrl());
            }
        } catch (\Magento\Exception $e) {
            $result .= sprintf($template, $this->_getNotFoundUrl());
        }
        return $result;
    }

    /**
     * Retrieve Content Type
     *
     * @return string
     */
    public function getContentType()
    {
        if (empty($this->_data['content_type'])) {
            $this->_data['content_type'] = $this->getMediaType() . '; charset=' . $this->getCharset();
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
            $this->_data['media_type'] = $this->_storeConfig->getConfig('design/head/default_media_type');
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
            $this->_data['charset'] = $this->_storeConfig->getConfig('design/head/default_charset');
        }
        return $this->_data['charset'];
    }

    /**
     * Set title element text
     *
     * @param string|array $title
     * @return \Magento\Page\Block\Html\Head
     */
    public function setTitle($title)
    {
        if (is_array($title)) {
            $this->_titleChunks = $title;
            $title = implode(' / ', $title);
        } else {
            $this->_pureTitle = $title;
        }
        $this->_data['title'] = $this->_storeConfig->getConfig('design/head/title_prefix') . ' ' . $title
            . ' ' . $this->_storeConfig->getConfig('design/head/title_suffix');
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
     * Same as getTitle(), but return only first item from chunk for backend pages
     *
     * @return mixed|string
     */
    public function getShortTitle()
    {
        if (!empty($this->_titleChunks)) {
            return reset($this->_titleChunks);
        } else {
            return $this->_pureTitle;
        }
    }

    /**
     * Retrieve default title text
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return $this->_storeConfig->getConfig('design/head/default_title');
    }

    /**
     * Retrieve content for description tag
     *
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->_data['description'])) {
            $this->_data['description'] = $this->_storeConfig->getConfig('design/head/default_description');
        }
        return $this->_data['description'];
    }

    /**
     * Retrieve content for keywords tag
     *
     * @return string
     */
    public function getKeywords()
    {
        if (empty($this->_data['keywords'])) {
            $this->_data['keywords'] = $this->_storeConfig->getConfig('design/head/default_keywords');
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
            $this->_data['robots'] = $this->_storeConfig->getConfig('design/search_engine_robots/default_robots');
        }
        return $this->_data['robots'];
    }

    /**
     * Get miscellaneous scripts/styles to be included in head before head closing tag
     *
     * @return string
     */
    public function getIncludes()
    {
        if (empty($this->_data['includes'])) {
            $this->_data['includes'] = $this->_storeConfig->getConfig('design/head/includes');
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
        $folderName = \Magento\Backend\Model\Config\Backend\Image\Favicon::UPLOAD_DIR;
        $storeConfig = $this->_storeConfig->getConfig('design/head/shortcut_icon');
        $faviconFile = $this->_storeManager->getStore()->getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = $this->_dirs->getDir('media') . '/' . $folderName . '/' . $storeConfig;

        if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            $url = $faviconFile;
        } else {
            $url = $this->getViewFileUrl('Magento_Page::favicon.ico');
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename
     * @return bool
     */
    protected function _isFile($filename)
    {
        if ($this->_fileStorageDatabase->checkDbUsage() && !is_file($filename)) {
            $this->_fileStorageDatabase->saveFileToFilesystem($filename);
        }
        return is_file($filename);
    }

    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocale()
    {
        return substr($this->_locale->getLocaleCode(), 0, 2);
    }
}
