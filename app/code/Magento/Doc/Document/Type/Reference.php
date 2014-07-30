<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;

class Reference extends AbstractType implements ReferenceInterface
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
        $filePath = $item['reference'] . '.xhtml';
        $result = $this->content->get($filePath);
        if (!$result) {
            $result = "Broken reference to '{$filePath}'";
        }
        return $result;
    }
}
