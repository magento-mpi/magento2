<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

class Generator
{
    /**
     * @var GeneratorInterface[]
     */
    protected $generators = [];

    /**
     * Constructor
     *
     * @param array $generators
     */
    public function __construct(
        array $generators = null
    ) {
        $this->generators = $generators;
    }

    /**
     * Traverse through all elements of specified XML-node and schedule structural elements of it
     *
     * @param Reader\Context $readerContext
     * @return $this
     */
    public function generate(Reader\Context $readerContext)
    {
        return $this;
    }

    protected function _generateScheduledStructure(ScheduledStructure $scheduledStructure)
    {
        while (false === $scheduledStructure->isStructureEmpty()) {
            $this->_scheduleElement(key($this->_scheduledStructure->getStructure()));
        }
        $scheduledStructure->flushPaths();

        foreach ($scheduledStructure->getListToMove() as $elementToMove) {
            $this->_moveElementInStructure($elementToMove);
        }

        foreach ($scheduledStructure->getListToRemove() as $elementToRemove) {
            $this->_removeElement($elementToRemove);
        }

        while (false === $scheduledStructure->isElementsEmpty()) {
            // list($type, $node, $actions, $args, $attributes)
            list($type, $node, , , $attributes) = current($scheduledStructure->getElements());
            $elementName = key($scheduledStructure->getElements());

            if ($type == Element::TYPE_BLOCK) {
                $this->_generateBlock($elementName);
            } else {
                $this->_generateContainer($elementName, (string)$node[Element::CONTAINER_OPT_LABEL], $attributes);
                $scheduledStructure->unsetElement($elementName);
            }
        }
    }
}