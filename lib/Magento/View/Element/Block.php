<?php

namespace Magento\View\Element;

use Magento\ObjectManager;
use Magento\View\Element;
use Magento\App\Context;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\View\Render\Html;

class Block extends Base implements Element
{
    const TYPE = 'block';

    /**
     * Wrapped Element class name
     *
     * @var string
     */
    protected $class;

    /**
     * Wrapped Element Instance
     *
     * @var \Magento\Core\Block\AbstractBlock
     */
    protected $wrappedElement;

    /**
     * @param Context $context
     * @param RenderFactory $renderFactory
     * @param ViewFactory $viewFactory
     * @param ObjectManager $objectManager
     * @param Container $parent
     * @param array $meta
     * @throws \Exception
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Container $parent = null,
        array $meta = array()
    )
    {
        parent::__construct($context, $renderFactory, $viewFactory, $objectManager, $parent, $meta);

        if (!class_exists($this->meta['class'])) {
            throw new \Exception(__('Invalid block class name: ' . $this->meta['class']));
        }
    }

    public function register(Element $parent = null)
    {
        if (isset($parent)) {
            $parent->attach($this, $this->alias);
        }

        if ($this->getChildren()) {
            foreach ($this->getChildren() as $child) {
                $metaElement = $this->viewFactory->create($child['type'],
                    array(
                        'context' => $this->context,
                        'parent' => $this,
                        'meta' => $child
                    )
                );
                $metaElement->register($this);
            }
        }

        $this->wrappedElement = $this->objectManager->create($this->meta['class'],
            array('container' => $this, 'data' => $this->arguments));
        $this->addDataProvider($this->getName(), $this->wrappedElement);
    }

    public function render($type = Html::TYPE_HTML)
    {
        if (isset($this->meta['template'])) {
            $this->wrappedElement->setTemplate($this->meta['template']);
        }

        return $this->wrappedElement->toHtml();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param $method
     * @param array $arguments
     */
    public function call($method, array $arguments)
    {
        if ($this->wrappedElement) {
            call_user_func_array(array($this->wrappedElement, $method), $arguments);
        }
    }

    public function getWrappedElement()
    {
        return $this->wrappedElement;
    }
}
