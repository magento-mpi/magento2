<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

class Reader
{
    /**
     * @var ReaderInterface[]
     */
    protected $readers = [];

    /**
     * @var ReaderInterface[]
     */
    protected $nodeReaders = [];

    /**
     * Constructor
     *
     * @param Reader\Block $blockReader
     * @param Reader\Container $containerReader
     * @param Reader\Move $moveReader
     * @param Reader\Remove $removeReader
     * @param array $readers
     */
    public function __construct(
        Reader\Block $blockReader,
        Reader\Container $containerReader,
        Reader\Move $moveReader,
        Reader\Remove $removeReader,
        array $readers = null
    ) {
        $this->readers[] = $blockReader;
        $this->readers[] = $containerReader;
        $this->readers[] = $moveReader;
        $this->readers[] = $removeReader;
        $this->prepareReader();
    }

    protected function prepareReader()
    {
        /** @var $reader ReaderInterface */
        foreach ($this->readers as $reader) {
            foreach ($reader->getSupportedNodes() as $nodeName) {
                $this->nodeReaders[$nodeName] = $reader;
            }
        }
    }

    /**
     * Traverse through all elements of specified XML-node and schedule structural elements of it
     *
     * @param Reader\Context $readerContext
     * @param Element $rootNode
     * @return $this
     */
    public function readStructure(Reader\Context $readerContext, Element $rootNode)
    {
        /** @var $currentNode Element */
        foreach ($rootNode as $currentNode) {
            $elementName = $currentNode->getName();
            if (!isset($this->nodeReaders[$elementName])) {
                continue;
            }
            /** @var $reader ReaderInterface */
            $reader = $this->nodeReaders[$elementName];
            if ($reader->process($readerContext, $currentNode, $rootNode)) {
                $this->readStructure($readerContext, $currentNode);
            }
        }
        return $this;
    }
}