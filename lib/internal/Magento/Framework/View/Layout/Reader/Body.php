<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

class Body implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_BODY = 'body';
    /**#@-*/

    /**
     * @var \Magento\Framework\View\Page\Config\Reader
     */
    protected $pageConfigReader;

    /**
     * @param \Magento\Framework\View\Page\Config\Reader $pageConfigReader
     */
    public function __construct(
        \Magento\Framework\View\Page\Config\Reader $pageConfigReader
    ) {
        $this->pageConfigReader = $pageConfigReader;
    }

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_BODY];
    }

    /**
     * {@inheritdoc}
     *
     * @param Context $readerContext
     * @param Layout\Element $currentElement
     * @param Layout\Element $parentElement
     * @return $this
     */
    public function process(Context $readerContext, Layout\Element $currentElement, Layout\Element $parentElement)
    {
        $this->pageConfigReader->readBody($currentElement);
        return false;
    }
}
