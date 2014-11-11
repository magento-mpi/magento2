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

    /** @var string[] */
    private $prependedValues = [];

    /** @var string[] */
    private $appendedValues = [];

    /**
     * @var string
     */
    private $textValue;

    /**
     * @param App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set page title
     *
     * @param string|array $title
     * @return $this
     */
    public function set($title)
    {
        $this->textValue = $title;
        return $this;
    }

    /**
     * Retrieve title element text (encoded)
     *
     * @param string $glue
     * @return string
     */
    public function get($glue = ' / ')
    {
        $title = array_merge($this->prependedValues, [$this->getShort()], $this->appendedValues);
        return join($glue, $title);
    }

    /**
     * Same as getTitle(), but return only first item from chunk
     *
     * @return mixed
     */
    public function getShort()
    {
        return $this->prepare($this->textValue);
    }

    /**
     * @param string $title
     * @return string
     */
    protected function prepare($title)
    {
        return $this->scopeConfig->getValue(
            'design/head/title_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) . ' ' . $title . ' ' . $this->scopeConfig->getValue(
            'design/head/title_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve default title text
     *
     * @return string
     */
    public function getDefault()
    {
        $defaultTitle = $this->scopeConfig->getValue(
            'design/head/default_title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $this->prepare($defaultTitle);
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
