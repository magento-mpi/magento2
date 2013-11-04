<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

/**
 * Filter for standard strip_tags() function with extra functionality for html entities
 */
class StripTags implements \Zend_Filter_Interface
{
    /**
     * @var \Magento\Escaper
     */
    protected $escaper;

    /**
     * @var string
     */
    protected $allowableTags;

    /**
     * @var bool
     */
    protected $escape;

    /**
     * @param \Magento\Escaper $escaper
     * @param null $allowableTags
     * @param bool $escape
     */
    public function __construct(\Magento\Escaper $escaper, $allowableTags = null, $escape = false)
    {
        $this->escaper = $escaper;
        $this->allowableTags = $allowableTags;
        $this->escape = $escape;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $result = strip_tags($value, $this->allowableTags);
        return $this->escape ? $this->escaper->escapeHtml($result, $this->allowableTags) : $result;
    }
}
