<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

use Magento\Framework\View\LayoutInterface;

class GeneratorPool
{
    /**
     * @var ScheduledStructure\Helper
     */
    protected $helper;

    /**
     * @var GeneratorInterface[]
     */
    protected $generators = [];

    /**
     * Constructor
     *
     * @param ScheduledStructure\Helper $helper
     * @param Generator\Block $blockGenerator
     * @param Generator\Container $containerGenerator
     * @param array $generators
     */
    public function __construct(
        ScheduledStructure\Helper $helper,
        Generator\Block $blockGenerator,
        Generator\Container $containerGenerator,
        array $generators = null
    ) {
        $this->helper = $helper;
        $this->generators[$blockGenerator->getType()] = $blockGenerator;
        $this->generators[$containerGenerator->getType()] = $containerGenerator;
    }

    /**
     * Get generator
     *
     * @param string $type
     * @return GeneratorInterface
     * @throws \InvalidArgumentException
     */
    public function getGenerator($type)
    {
        if (!isset($this->generators[$type])) {
            throw new \InvalidArgumentException("Invalid generator type '{$type}'");
        }
        return $this->generators[$type];
    }

    /**
     * Traverse through all elements of specified XML-node and schedule structural elements of it
     *
     * @param Reader\Context $readerContext
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @throws \Magento\Framework\Exception
     * @return array
     */
    public function process(Reader\Context $readerContext, LayoutInterface $layout)
    {
        $generated = [];
        $this->buildStructure($readerContext);
        $scheduledStructure = $readerContext->getScheduledStructure();
        while (false === $scheduledStructure->isElementsEmpty()) {
            $elementName = key($scheduledStructure->getElements());
            $generated[$elementName] = $this->processElement($readerContext, $elementName, $layout);
        }
        return $generated;
    }

    /**
     * @param Reader\Context $readerContext
     * @param string $elementName
     * @param $layout
     * @return $this|null
     * @throws \Magento\Framework\Exception
     */
    public function processElement(Reader\Context $readerContext, $elementName, $layout)
    {
        $element = null;
        $scheduledStructure = $readerContext->getScheduledStructure();
        list($type) = $scheduledStructure->getElement($elementName);
        if (isset($this->generators[$type])) {
            /** @var GeneratorInterface $generator */
            $generator = $this->generators[$type];
            $element = $generator->process($readerContext, $elementName, $layout);
        } else {
            throw new \Magento\Framework\Exception(
                "Unexpected element type specified for generating: {$type}."
            );
        }
        $scheduledStructure->unsetElement($elementName);
        return $element;
    }

    /**
     * Build structure that is based on scheduled structure
     *
     * @param Reader\Context $readerContext
     * @return $this
     */
    protected function buildStructure(Reader\Context $readerContext)
    {
        //Schedule all element into nested structure
        $scheduledStructure = $readerContext->getScheduledStructure();
        while (false === $scheduledStructure->isStructureEmpty()) {
            $this->helper->scheduleElement(
                $scheduledStructure,
                $readerContext->getStructure(),
                key($scheduledStructure->getStructure())
            );
        }
        $scheduledStructure->flushPaths();
        foreach ($scheduledStructure->getListToMove() as $elementToMove) {
            $this->moveElementInStructure($readerContext, $elementToMove);
        }
        foreach ($scheduledStructure->getListToRemove() as $elementToRemove) {
            $this->removeElement($readerContext, $elementToRemove);
        }
        return $this;
    }

    /**
     * Remove scheduled element
     *
     * @param Reader\Context $readerContext
     * @param string $elementName
     * @param bool $isChild
     * @return $this
     */
    protected function removeElement(Reader\Context $readerContext, $elementName, $isChild = false)
    {
        $scheduledStructure = $readerContext->getScheduledStructure();
        $structure = $readerContext->getStructure();

        $elementsToRemove = array_keys($structure->getChildren($elementName));
        $scheduledStructure->unsetElement($elementName);
        foreach ($elementsToRemove as $element) {
            $this->removeElement($readerContext, $element, true);
        }
        if (!$isChild) {
            $structure->unsetElement($elementName);
            $scheduledStructure->unsetElementFromListToRemove($elementName);
        }
        return $this;
    }

    /**
     * Move element in scheduled structure
     *
     * @param Reader\Context $readerContext
     * @param string $element
     * @return $this
     */
    protected function moveElementInStructure(Reader\Context $readerContext, $element)
    {
        $scheduledStructure = $readerContext->getScheduledStructure();
        $structure = $readerContext->getStructure();
        list($destination, $siblingName, $isAfter, $alias) = $scheduledStructure->getElementToMove($element);
        $childAlias = $structure->getChildAlias($structure->getParentId($element), $element);
        if (!$alias && false === $structure->getChildId($destination, $childAlias)) {
            $alias = $childAlias;
        }
        $structure->unsetChild($element, $alias)->setAsChild($element, $destination, $alias);
        $this->helper->reorderChild($structure, $destination, $element, $siblingName, $isAfter);
        return $this;
    }
}
