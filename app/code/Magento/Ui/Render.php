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
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class Render
 */
class Render extends AbstractBlock
{
    /**
     * Ui element view
     *
     * @var ViewInterface
     */
    protected $view;

    /**
     * Render context
     *
     * @var Context
     */
    protected $renderContext;

    /**
     * Ui element view factory
     *
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
     * Get component name
     *
     * @return string
     */
    public function getComponent()
    {
        return $this->getData('configuration/component');
    }

    /**
     * Get layout handle
     *
     * @return string
     */
    public function getLayoutHandle()
    {
        return $this->getData('configuration/name');
    }

    /**
     * Prepare private layout object
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->privateLayout->addHandle($this->getLayoutHandle());
        $this->privateLayout->loadLayout();

        $this->renderContext->setPageLayout($this->getLayout());

        $this->view = $this->getUiElementView($this->getComponent());
        $this->renderContext->setRootView($this->view);
        $this->prepare($this->view, $this->getData('configuration'));


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
     * Render Ui Element content
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function render()
    {
        return $this->view->render($this->renderContext->getAcceptType());
    }

    /**
     * Prepare UI Element View
     *
     * @param BlockInterface $view
     * @param array $arguments
     */
    protected function prepare(BlockInterface $view, array $arguments = [])
    {
        if ($view instanceof AbstractView) {
            $view->prepare($arguments);
        }
        foreach ($view->getLayout()->getChildNames($view->getNameInLayout()) as $child) {
            $this->prepare($view->getChildBlock($child));
        }
    }

    /**
     * Get UI Element View
     *
     * @param string $uiElementName
     * @return ViewInterface
     * @throws \InvalidArgumentException
     */
    protected function getUiElementView($uiElementName)
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
