<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;

class Api extends AbstractType implements ApiInterface
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
        list ($class, $method) = explode('::', $item['reference']);
        $result = $this->content->get($filePath);
        if (!$result) {
            $result = "<h4>{$class}</h4><h5>{$method}</h5><h6>Arguments</h6><ul><li>...</li></ul>";
        }
        return $result;
    }
}
