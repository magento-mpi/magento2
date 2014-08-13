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
    /**
     * Constants of available types
     */
    const ELEMENT_TYPE_BODY = 'body';
    const ELEMENT_TYPE_HTML = 'html';
    /**  */

    /**
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
     * @var \Magento\Framework\View\Asset\Collection
     */
    protected $assetCollection;

    /**
     * @var string[][]
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $pageLayout;

    /**
     * @var \Magento\Framework\View\PageLayout\Config
     */
    protected $layoutConfig;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Asset\Collection $assetCollection
     * @param \Magento\Core\Model\PageLayout\Config\Builder $configBuilder
     */
    public function __construct(
        \Magento\Framework\View\Asset\Collection $assetCollection,
        \Magento\Core\Model\PageLayout\Config\Builder $configBuilder
    ) {
        $this->assetCollection = $assetCollection;
        $this->layoutConfig = $configBuilder->getPageLayoutsConfig();
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
     * @return \Magento\Framework\View\Asset\Collection
     */
    public function getAssetCollection()
    {
        return $this->assetCollection;
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
        if (!$this->layoutConfig->hasPageLayout($handle)) {
            throw new \UnexpectedValueException($handle . ' page layout does not exist.');
        }
        $this->pageLayout = $handle;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageLayout()
    {
        return $this->pageLayout;
    }
}
