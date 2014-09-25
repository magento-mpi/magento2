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
     * @param Reader\Body\Block $blockReader
     * @param Reader\Body\Container $containerReader
     * @param Reader\Move $moveReader
     * @param Reader\Remove $removeReader
     * @param Reader\UiComponent $uiComponent
     * @param Reader\Html $html
     * @param Reader\Head $head
     * @param Reader\Body $body
     * @param array $readers
     */
    public function __construct(
        Reader\Body\Block $blockReader,
        Reader\Body\Container $containerReader,
        Reader\Move $moveReader,
        Reader\Remove $removeReader,
        Reader\UiComponent $uiComponent,
        Reader\Html $html,
        Reader\Head $head,
        Reader\Body $body,
        array $readers = null
    ) {
        $this->readers[] = $blockReader;
        $this->readers[] = $containerReader;
        $this->readers[] = $moveReader;
        $this->readers[] = $removeReader;
        $this->readers[] = $uiComponent;
        $this->readers[] = $html;
        $this->readers[] = $head;
        $this->readers[] = $body;
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