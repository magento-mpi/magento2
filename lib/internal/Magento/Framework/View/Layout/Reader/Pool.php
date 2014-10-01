<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

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
     * @var ReaderInterface[]
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
     * @param ReaderFactory $readerFactory
     * @param array $readers
     */
    public function __construct(ReaderFactory $readerFactory, array $readers = [])
    {
        $this->readerFactory = $readerFactory;
        $this->readers = $readers;
    }

    /**
     * Register supported nodes and readers
     *
     * @param array $readers
     */
    protected function prepareReader($readers)
    {
        /** @var $reader ReaderInterface */
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
     * @param Reader\Context $readerContext
     * @param Element $element
     * @return $this
     */
    public function readStructure(Reader\Context $readerContext, Element $element)
    {
        $this->prepareReader($this->readers);

        /** @var $node Element */
        foreach ($element as $node) {
            $nodeName = $node->getName();
            if (!isset($this->nodeReaders[$nodeName])) {
                continue;
            }
            /** @var $reader ReaderInterface */
            $reader = $this->nodeReaders[$nodeName];
            $reader->process($readerContext, $node, $element);
        }
        return $this;
    }
}
