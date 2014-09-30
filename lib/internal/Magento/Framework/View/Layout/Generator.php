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
     * @param array $generators
     */
    public function __construct(
        ScheduledStructure\Helper $helper,
        array $generators = null
    ) {
        $this->helper = $helper;
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
        $scheduledStructure = $readerContext->getScheduledStructure();
        $structure = $readerContext->getStructure();
        while (false === $scheduledStructure->isStructureEmpty()) {
            $this->helper->scheduleElement(
                $scheduledStructure,
                $structure,
                key($scheduledStructure->getStructure())
            );
        }
        return $this;
    }

    protected function _generateScheduledStructure(ScheduledStructure $scheduledStructure)
    {

        while (false === $scheduledStructure->isElementsEmpty()) {
            list($type) = current($scheduledStructure->getElements());
            $elementName = key($scheduledStructure->getElements());

            if ($type == Element::TYPE_UI_COMPONENT) {
                $this->_generateUiComponent($elementName);
            } elseif ($type == Element::TYPE_BLOCK) {
                $this->_generateBlock($elementName);
            } elseif ($type == Element::TYPE_CONTAINER) {
                $this->_generateContainer($elementName);
                $scheduledStructure->unsetElement($elementName);
            } else {
                throw new \Magento\Framework\Exception(
                    "Unexpected element type specified for generating: {$type}."
                );
            }
        }
    }
}