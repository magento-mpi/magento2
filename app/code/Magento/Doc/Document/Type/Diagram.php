<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;

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
     * @param string $filePath
     * @param array $item
     * @return string
     */
    public function getContent($filePath, $item)
    {
        $filePath = $item['scheme'] . '/' . $item['name'] . '.xhtml';
        return $this->content->get($filePath);
    }
}
