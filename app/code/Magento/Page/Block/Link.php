<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block;

/**
 * Block representing link
 *
 * @method string getLabel()
 * @method string getPath()
 * @method string getTitle()
 */
class Link extends \Magento\Core\Block\Template
{
    /** @var string */
    protected $_template = 'Magento_Page::link.phtml';

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath());
    }
}
