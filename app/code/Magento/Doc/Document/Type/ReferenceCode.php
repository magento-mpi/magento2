<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Content;

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
     * Get item's content
     *
     * @param string $filePath
     * @param array $item
     * @return string
     */
    public function getContent($filePath, $item)
    {
        $filePath = $item['scheme'] . '/' . $item['name'] . '.xhtml';
        $result = $this->content->get($filePath);
        if (!$result) {
            list ($class, $method) = explode('::', $item['reference']);
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
