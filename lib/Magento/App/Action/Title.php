<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

class Title
{
    /**
     * Title parts to be rendered in the page head title
     *
     * @var string[]
     */
    protected $_titles = array();

    /**
     * @param string $text
     * @param bool $prepend
     * @return $this
     */
    public function add($text, $prepend = false)
    {
        if ($prepend) {
            array_unshift($this->_titles, $text);
        } else {
            $this->_titles[] = $text;
        }
        return $this;
    }

    /**
     * Get titles
     *
     * @return string[]
     */
    public function get()
    {
        return $this->_titles;
    }
}
