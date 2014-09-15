<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Ui\Render\Layout;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class Render
 */
class Render extends AbstractBlock
{
    /**
     * Render context
     *
     * @var Context
     */
    protected $renderContext;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * Private layout
     *
     * @var Layout
     */
    protected $privateLayout;

    /**
     * Constructor
     *
     * @param Context $renderContext
     * @param Template\Context $context
     * @param ViewFactory $viewFactory
     * @param Layout $privateLayout
     * @param array $data
     */
    public function __construct(
        Context $renderContext,
        Template\Context $context,
        ViewFactory $viewFactory,
        Layout $privateLayout,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->viewFactory = $viewFactory;
        $this->privateLayout = $privateLayout;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->privateLayout->addHandle('ui_' . $this->getComponent());
        $this->privateLayout->addHandle($this->getName());
        $this->privateLayout->loadLayout();

        return parent::_prepareLayout();
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->render($this->getComponent(), []);
    }

    /**
     * Render UI Element content
     *
     * @param string $uiElementName
     * @param array $arguments
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function render($uiElementName, array $arguments = [])
    {
        // Obtain concrete UI Element View
        $view = $this->getUiElementView($uiElementName);
        $useArguments = array_replace($this->getData(), $arguments);
        $useArguments['name'] = isset($useArguments['name']) ? $useArguments['name'] : $this->getName();

        return $view->render($useArguments, $this->renderContext->getAcceptType());
    }

    /**
     * @param string $uiElementName
     * @param array $data
     * @return ViewInterface
     * @throws \InvalidArgumentException
     */
    protected function getUiElementView($uiElementName, array $data = [])
    {
        /** @var \Magento\Ui\ViewInterface $view */
        $view = $this->privateLayout->getBlock($uiElementName);
        if (!$view instanceof ViewInterface) {
            throw new \InvalidArgumentException(
                sprintf('UI Element "%s" must implement \Magento\Ui\ViewInterface', $uiElementName)
            );
        }

        return $view;
    }
}
