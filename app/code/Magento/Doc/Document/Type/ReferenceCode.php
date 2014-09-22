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
 * Class ReferenceCode
 * @package Magento\Doc\Document\Type
 */
class ReferenceCode extends AbstractType implements ReferenceInterface
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
     * Get code content
     *
     * @param Item $item
     * @return string
     */
    public function getContent(Item $item)
    {
        $filePath = $this->getFilePath($item);
        $result = $this->content->get($filePath);
        if (!$result) {
            list ($class, $method) = explode('::', $item->getData('reference'));
            $refMethod = new \ReflectionMethod($class, $method);
            $start = $refMethod->getStartLine();
            $end = $refMethod->getEndLine();
            $refFile = new \SplFileObject($refMethod->getFileName());
            $refFile->seek($start-1);
            $content = "<?php\n";
            while($refFile->key() < $end) {
                $content .= $refFile->current();
                $refFile->next();
            }
            $result = highlight_string($content, true);
        }
        return $result;
    }
}
