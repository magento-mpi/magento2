<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;
use Magento\Doc\Document\Item;

/**
 * Class Diagram
 * @package Magento\Doc\Document\Type
 */
class Diagram extends AbstractType implements DiagramInterface
{
    /**
     * @var Content
     */
    protected $content;

    /**
     * Constructor
     *
     * @param Content $content
     */
    public function __construct(
        Content $content
    ) {
        $this->content = $content;
    }

    /**
     * Get item's content
     *
     * @param Item $item
     * @return string
     */
    public function getContent(Item $item)
    {
        $filePath = $item->getData('scheme') . '/' . $item->getData('name') . '.xhtml';
        return $this->content->get($filePath);
    }
}
