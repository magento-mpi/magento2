<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Element\UiComponentFactory;

class UiComponent implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_UI_COMPONENT = 'ui_component';
    /**#@-*/

    /**
     * @var Layout\ScheduledStructure\Helper
     */
    protected $layoutHelper;

    /**
     * @var UiComponentFactory
     */
    protected $uiComponentFactory;

    /**
     * Construct
     *
     * @param Layout\ScheduledStructure\Helper $helper
     * @param UiComponentFactory $uiComponentFactory
     */
    public function __construct(
        Layout\ScheduledStructure\Helper $helper,
        UiComponentFactory $uiComponentFactory
    ) {
        $this->layoutHelper = $helper;
        $this->uiComponentFactory = $uiComponentFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_UI_COMPONENT];
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
        $this->layoutHelper->scheduleStructure(
            $readerContext->getScheduledStructure(),
            $currentElement,
            $parentElement
        );
        return false;
    }
}
