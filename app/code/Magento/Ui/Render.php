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
 * Base price render
 *
 * @method string getPriceRenderHandle()
 */
class Render extends AbstractBlock
{
    /**
     * Default type renderer
     *
     * @var string
     */
    protected $defaultTypeRender = 'default';

    /**
     * Private layout
     *
     * @var Layout
     */
    protected $privateLayout;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Layout $privateLayout
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Layout $privateLayout,
        array $data = []
    ) {
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
        return $this->render($this->getComponent());
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
    public function render($uiElementName, array $arguments = [])
    {
        $useArguments = array_replace($this->getData(), $arguments);
        $useArguments['config']['name'] = isset($useArguments['name'])
            ? $useArguments['name']
            : $this->getName();

        // obtain concrete UI Element View
        $view = $this->getUiElementView($uiElementName, $useArguments);
        return $view->render($useArguments, $this->getAcceptType(), $this->_request->getParams());
    }

    /**
     * @param string $uiElementName
     * @param array $data
     * @throws \InvalidArgumentException
     * @return ViewInterface
     */
    protected function getUiElementView(
        $uiElementName,
        array $data = []
    ) {
        /** @var \Magento\Ui\ViewInterface $view */
        $view = $this->privateLayout->getBlock($uiElementName);
        if (!$view instanceof ViewInterface) {
            throw new \InvalidArgumentException(
                'UI Element "' . $uiElementName . '" must implement \Magento\Ui\ViewInterface'
            );
        }

        return $view;
    }

    /**
     * Getting requested accept type
     *
     * @return string
     */
    protected function getAcceptType()
    {
        $rawAcceptType = $this->_request->getHeader('Accept');
        if (strpos($rawAcceptType, 'json') !== false) {
            $acceptType = 'json';
        } else {
            if (strpos($rawAcceptType, 'text/html') !== false) {
                $acceptType = 'html';
            } else {
                $acceptType = 'xml';
            }
        }

        return $acceptType;
    }
}
