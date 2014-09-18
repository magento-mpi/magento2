<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Framework\Object;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Render
 */
class Render extends Object
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
     * Layout Interface
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var boolean
     */
    protected $layoutLoaded;

    /**
     * Constructor
     *
     * @param Context $renderContext
     * @param LayoutFactory $layoutFactory
     * @param array $data
     */
    public function __construct(
        Context $renderContext,
        LayoutFactory $layoutFactory,
        array $data = []
    ) {
        $this->renderContext = $renderContext;
        $this->renderContext->setRender($this);
        $this->layoutFactory = $layoutFactory;
        parent::__construct($data);
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
     * @param LayoutInterface $layout
     * @return void
     */
    public function setLayout(LayoutInterface $layout)
    {
        if (!$this->renderContext->getPageLayout()) {
            $this->renderContext->setPageLayout($layout);
        }
    }

    /**
     * Create Ui Component instance
     *
     * @param string $componentName
     * @param string $handleName
     * @param array $arguments
     * @return ViewInterface
     */
    public function createUiComponent($componentName, $handleName = '', array $arguments = [])
    {
        $root = false;
        if (!$this->renderContext->getNamespace()) {
            $root = true;
            if ($handleName) {
                $this->renderContext->setNamespace($handleName);
            }
        }

        if ($root && $handleName) {
            if (!$this->layoutLoaded) {
                $this->layoutLoaded = true;
                $this->layout = $this->layoutFactory->create();
                $this->layout->getUpdate()->addHandle('ui_components');
                $this->layout->getUpdate()->addHandle($handleName);
                $this->loadLayout();
            }
        }

        $view = $this->getUiElementView($componentName);

        $view->update($arguments);
        if ($root) {
            // data should be prepared starting from the root element
            $this->prepare($view);
            $this->renderContext->setNamespace(null);
        }
        return $view;
    }

    /**
     * Prepare UI Component data
     *
     * @param object $view
     * @return void
     */
    protected function prepare($view)
    {
        if ($view instanceof ViewInterface) {
            $view->prepare();
        }
        foreach ($view->getLayout()->getChildNames($view->getNameInLayout()) as $childName) {
            $child = $view->getChildBlock($childName);
            $this->prepare($child);
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
        $view = $this->layout->getBlock($uiElementName);
        if (!$view instanceof ViewInterface) {
            throw new \InvalidArgumentException(
                sprintf('UI Element "%s" must implement \Magento\Ui\ViewInterface', $uiElementName)
            );
        }
        return $view;
    }

    /**
     * Load layout
     *
     * @return void
     */
    protected function loadLayout()
    {
        $this->layout->getUpdate()->load();
        $this->layout->generateXml();
        $this->layout->generateElements();
    }
}
