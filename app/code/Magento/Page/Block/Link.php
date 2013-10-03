<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Block representing link
 *
 * @method string getHref()
 * @method string getLabel()
 * @method string getTitle()
 */
namespace Magento\Page\Block;

class Link extends \Magento\Core\Block\Template
{
    /** @var string */
    protected $_template = 'Magento_Page::link.phtml';
}
