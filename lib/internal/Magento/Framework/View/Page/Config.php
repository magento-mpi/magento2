<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Page;

/**
 * An API for page configuration
 *
 * Has methods for managing properties specific to web pages:
 * - title
 * - related documents, linked static assets in particular
 * - meta info
 * - root element properties
 * - etc...
 */
class Config
{
    /**#@+
     * Constants of available types
     */
    const ELEMENT_TYPE_BODY = 'body';
    const ELEMENT_TYPE_HTML = 'html';
    /**#@-*/

    /**
     * Allowed group of types
     *
     * @var array
     */
    private $allowedTypes = [
        self::ELEMENT_TYPE_BODY,
        self::ELEMENT_TYPE_HTML
    ];

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $titleChunks;

    /**
     * @var string
     */
    protected $pureTitle;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\View\Asset\GroupedCollection
     */
    protected $pageAssets;

    /**
     * @var string[][]
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $pageLayout;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\Asset\GroupedCollection $pageAssets
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\Asset\GroupedCollection $pageAssets,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->assetRepo = $assetRepo;
        $this->pageAssets = $pageAssets;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set page title
     *
     * @param string|array $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $this->scopeConfig->getValue(
            'design/head/title_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) . ' ' . $this->prepareTitle($title) . ' ' . $this->scopeConfig->getValue(
            'design/head/title_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $this;
    }

    /**
     * @param array|string $title
     * @return string
     */
    protected function prepareTitle($title)
    {
        $this->titleChunks = '';
        $this->pureTitle = '';

        if (is_array($title)) {
            $this->titleChunks = $title;
            return implode(' / ', $title);
        }
        $this->pureTitle = $title;
        return $this->pureTitle;
    }

    /**
     * Retrieve title element text (encoded)
     *
     * @return string
     */
    public function getTitle()
    {
        if (empty($this->title)) {
            $this->title = $this->getDefaultTitle();
        }
        return htmlspecialchars(html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Same as getTitle(), but return only first item from chunk for backend pages
     *
     * @return mixed
     */
    public function getShortTitle()
    {
        if (!empty($this->titleChunks)) {
            return reset($this->titleChunks);
        } else {
            return $this->pureTitle;
        }
    }

    /**
     * Retrieve default title text
     *
     * @return string
     */
    public function getDefaultTitle()
    {
        return $this->scopeConfig->getValue(
            'design/head/default_title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->metadata['content_type'] = $contentType;
    }

    /**
     * Retrieve Content Type
     *
     * @return string
     */
    public function getContentType()
    {
        if (empty($this->metadata['content_type'])) {
            $this->metadata['content_type'] = $this->getMediaType() . '; charset=' . $this->getCharset();
        }
        return $this->metadata['content_type'];
    }

    /**
     * @param string $mediaType
     */
    public function setMediaType($mediaType)
    {
        $this->metadata['media_type'] = $mediaType;
    }

    /**
     * Retrieve Media Type
     *
     * @return string
     */
    public function getMediaType()
    {
        if (empty($this->metadata['media_type'])) {
            $this->metadata['media_type'] = $this->scopeConfig->getValue(
                'design/head/default_media_type',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->metadata['media_type'];
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->metadata['charset'] = $charset;
    }

    /**
     * Retrieve Charset
     *
     * @return string
     */
    public function getCharset()
    {
        if (empty($this->metadata['charset'])) {
            $this->metadata['charset'] = $this->scopeConfig->getValue(
                'design/head/default_charset',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->metadata['charset'];
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->metadata['description'] = $description;
    }

    /**
     * Retrieve content for description tag
     *
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->metadata['description'])) {
            $this->metadata['description'] = $this->scopeConfig->getValue(
                'design/head/default_description',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->metadata['description'];
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->metadata['keywords'] = $keywords;
    }

    /**
     * Retrieve content for keywords tag
     *
     * @return string
     */
    public function getKeywords()
    {
        if (empty($this->metadata['keywords'])) {
            $this->metadata['keywords'] = $this->scopeConfig->getValue(
                'design/head/default_keywords',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->metadata['keywords'];
    }

    /**
     * @param string $robots
     */
    public function setRobots($robots)
    {
        $this->metadata['robots'] = $robots;
    }

    /**
     * Retrieve URL to robots file
     *
     * @return string
     */
    public function getRobots()
    {
        if (empty($this->metadata['robots'])) {
            $this->metadata['robots'] = $this->scopeConfig->getValue(
                'design/search_engine_robots/default_robots',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->metadata['robots'];
    }

    /**
     * @return \Magento\Framework\View\Asset\GroupedCollection
     */
    public function getAssetCollection()
    {
        return $this->pageAssets;
    }

    /**
     * @param string $name
     * @param array $properties
     * @return $this
     */
    public function addPageAsset($name, array $properties = [])
    {
        $asset = $this->assetRepo->createAsset($name);
        $this->pageAssets->add($name, $asset, $properties);

        return $this;
    }

    /**
     * @param string $name
     * @param string $contentType
     * @param array $properties
     * @return $this
     */
    public function addRemotePageAsset($name, $contentType, array $properties = [])
    {
        $remoteAsset = $this->assetRepo->createRemoteAsset($name, $contentType);
        $this->pageAssets->add($name, $remoteAsset, $properties);

        return $this;
    }

    /**
     * Add RSS element
     *
     * @param string $title
     * @param string $href
     * @return $this
     */
    public function addRss($title, $href)
    {
        $remoteAsset = $this->assetRepo->createRemoteAsset((string)$href, 'unknown');
        $this->pageAssets->add(
            "link/{$href}",
            $remoteAsset,
            array('attributes' => 'rel="alternate" type="application/rss+xml" title="' . $title . '"')
        );

        return $this;
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return $this
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $bodyClasses = $this->getElementAttribute(self::ELEMENT_TYPE_BODY, 'classes');
        $this->setElementAttribute(self::ELEMENT_TYPE_BODY, 'classes', $bodyClasses . ' ' . $className);
        return $this;
    }

    /**
     * Set additional element attribute
     *
     * @param string $elementType
     * @param string $attribute
     * @param mixed $value
     * @return $this
     * @throws \Magento\Framework\Exception
     */
    public function setElementAttribute($elementType, $attribute, $value)
    {
        if (array_search($elementType, $this->allowedTypes) === false) {
            throw new \Magento\Framework\Exception($elementType . ' isn\'t allowed');
        }
        $this->elements[$elementType][$attribute] = $value;
        return $this;
    }

    /**
     * Retrieve additional element attribute
     *
     * @param string $elementType
     * @param string $attribute
     * @return null
     */
    public function getElementAttribute($elementType, $attribute)
    {
        return isset($this->elements[$elementType][$attribute]) ? $this->elements[$elementType][$attribute] : null;
    }

    /**
     * Set page layout
     *
     * @param string $handle
     * @return $this
     * @throws \UnexpectedValueException
     */
    public function setPageLayout($handle)
    {
        $this->pageLayout = $handle;
        return $this;
    }

    /**
     * Return current page layout
     *
     * @return string
     */
    public function getPageLayout()
    {
        return $this->pageLayout;
    }
}
