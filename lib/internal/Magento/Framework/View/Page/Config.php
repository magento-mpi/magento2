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
     * @param \Magento\Framework\View\Asset\Collection $assetCollection
     */
    public function __construct(
        \Magento\Framework\View\Asset\Collection $assetCollection
    ) {
        $this->assetCollection = $assetCollection;
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
     * @param string $elementType
     * @param string $attribute
     * @param $value
     * @return $this
     */
    public function setElementAttribute($elementType, $attribute, $value)
    {
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
     */
    public function setPageLayout($handle)
    {
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
