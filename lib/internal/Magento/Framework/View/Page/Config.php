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
     * Constructor
     *
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\Asset\GroupedCollection $pageAssets
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\Asset\GroupedCollection $pageAssets
    ) {
        $this->assetRepo = $assetRepo;
        $this->pageAssets = $pageAssets;
    }

    /**
     * Set page title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
