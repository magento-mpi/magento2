<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Page;

use Magento\Framework\App;
use Magento\Framework\View;

class Title
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @var string
     */
    private $titleChunks;

    /**
     * @var string
     */
    private $pureTitle;

    /** @var string[] */
    private $prependedValues = [];

    /** @var string[] */
    private $appendedValues = [];

    /**
     * @var string
     */
    private $value;

        /**
     * Set page title
     *
     * @param string|array $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->value = $this->scopeConfig->getValue(
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
        $preparedTitle = is_array($this->value) ? $this->value : [$this->value];
        $title = array_merge($this->prependedValues, $preparedTitle, $this->appendedValues);

        return join(' / ', $title);
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
     * @param string $suffix
     */
    public function append($suffix)
    {
        $this->appendedValues[] = $suffix;
    }

    /**
     * @param string $prefix
     */
    public function prepend($prefix)
    {
        array_unshift($this->prependedValues, $prefix);
    }
}
