<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;
use Magento\Doc\Document\Item;

/**
 * Class Example
 * @package Magento\Doc\Document\Type
 */
class Example extends AbstractType implements ExampleInterface
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
        $filePath = $this->getFilePath($item);
        return $this->content->get($filePath);
    }
}
