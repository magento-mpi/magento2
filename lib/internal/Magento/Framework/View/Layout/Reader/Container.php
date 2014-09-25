<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

class Container implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_CONTAINER = 'container';
    const TYPE_REFERENCE_CONTAINER = 'referenceContainer';
    /**#@-*/

    /**#@+
     * Names of container options in layout
     */
    const CONTAINER_OPT_HTML_TAG = 'htmlTag';
    const CONTAINER_OPT_HTML_CLASS = 'htmlClass';
    const CONTAINER_OPT_HTML_ID = 'htmlId';
    const CONTAINER_OPT_LABEL = 'label';
    /**#@-*/

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure\Helper
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Layout\ScheduledStructure\Helper $helper
     */
    public function __construct(Layout\ScheduledStructure\Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_CONTAINER, self::TYPE_REFERENCE_CONTAINER];
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
        switch ($currentElement->getName()) {
            case self::TYPE_CONTAINER:
                $this->helper->scheduleStructure(
                    $readerContext->getScheduledStructure(),
                    $currentElement,
                    $parentElement
                );
                $this->_mergeContainerAttributes($readerContext->getScheduledStructure(), $currentElement);
                break;

            case self::TYPE_REFERENCE_CONTAINER:
                $this->_mergeContainerAttributes($readerContext->getScheduledStructure(), $currentElement);
                break;

            default:
                break;
        }
        return true;
    }

    /**
     * Merge Container attributes
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\View\Layout\Element $currentElement
     * @return void
     */
    protected function _mergeContainerAttributes(
        Layout\ScheduledStructure $scheduledStructure,
        Layout\Element $currentElement
    ) {
        $containerName = $currentElement->getAttribute('name');
        $element = $scheduledStructure->getStructureElement($containerName, array());

        if (isset($element['attributes'])) {
            $keys = array_keys($element['attributes']);
            foreach ($keys as $key) {
                if (isset($currentElement[$key])) {
                    $element['attributes'][$key] = (string)$currentElement[$key];
                }
            }
        } else {
            $element['attributes'] = [
                self::CONTAINER_OPT_HTML_TAG   => (string)$currentElement[self::CONTAINER_OPT_HTML_TAG],
                self::CONTAINER_OPT_HTML_ID    => (string)$currentElement[self::CONTAINER_OPT_HTML_ID],
                self::CONTAINER_OPT_HTML_CLASS => (string)$currentElement[self::CONTAINER_OPT_HTML_CLASS],
                self::CONTAINER_OPT_LABEL      => (string)$currentElement[self::CONTAINER_OPT_LABEL]
            ];
        }
        $scheduledStructure->setStructureElement($containerName, $element);
    }
}
