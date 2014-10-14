<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

/**
 * Class Pool
 */
class Pool
{
    /**
     * @var array
     */
    protected $readers;

    /**
     * @var Layout\ReaderInterface[]
     */
    protected $nodeReaders = [];

    /**
     * Object Manager
     *
     * @var \Magento\Framework\View\Layout\ReaderFactory
     */
    protected $readerFactory;

    /**
     * Constructor
     *
     * @param Layout\ReaderFactory $readerFactory
     * @param array $readers
     */
    public function __construct(Layout\ReaderFactory $readerFactory, array $readers = [])
    {
        $this->readerFactory = $readerFactory;
        $this->readers = $readers;
    }

    /**
     * Register supported nodes and readers
     *
     * @param array $readers
     * @return void
     */
    protected function prepareReader($readers)
    {
        /** @var $reader Layout\ReaderInterface */
        foreach ($readers as $readerClass) {
            $reader = $this->readerFactory->create($readerClass);
            foreach ($reader->getSupportedNodes() as $nodeName) {
                $this->nodeReaders[$nodeName] = $reader;
            }
        }
    }

    /**
     * Traverse through all nodes
     *
     * @param Context $readerContext
     * @param Layout\Element $element
     * @return $this
     */
    public function readStructure(Context $readerContext, Layout\Element $element)
    {
        $this->prepareReader($this->readers);

        /** @var $node Layout\Element */
        foreach ($element as $node) {
            $nodeName = $node->getName();
            if (!isset($this->nodeReaders[$nodeName])) {
                continue;
            }
            /** @var $reader Layout\ReaderInterface */
            $reader = $this->nodeReaders[$nodeName];
            $reader->process($readerContext, $node, $element);
        }
        return $this;
    }
}
