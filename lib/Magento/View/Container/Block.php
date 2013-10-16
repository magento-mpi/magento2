<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\ObjectManager;
use Magento\View\Container as ContainerInterface;
use Magento\View\Context;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\View\Render\Html;

class Block extends Base implements ContainerInterface
{
    /**
     * Container type
     */
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
     * @param ContainerInterface $parent
     * @param array $meta
     * @throws \Exception
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        ContainerInterface $parent = null,
        array $meta = array()
    ) {
        parent::__construct($context, $renderFactory, $viewFactory, $objectManager, $parent, $meta);

        if (!class_exists($this->meta['class'])) {
            throw new \Exception(__('Invalid block class name: ' . $this->meta['class']));
        }
    }

    /**
     * @param ContainerInterface $parent
     */
    public function register(ContainerInterface $parent = null)
    {
        if (isset($parent)) {
            $parent->attach($this, $this->alias);
        }

        $this->wrappedElement = $this->objectManager->create($this->meta['class'],
            array(
                'container' => $this,
                'data' => $this->arguments,
            )
        );
        $this->wrappedElement->setNameInLayout($this->name);

        if (isset($this->meta['template'])) {
            $this->wrappedElement->setTemplate($this->meta['template']);
        }

        foreach ($this->getChildren() as $child) {
            $metaElement = $this->viewFactory->create($child['type'],
                array(
                    'context' => $this->context,
                    'parent' => $this,
                    'meta' => $child,
                )
            );
            $metaElement->register($this);
        }

        $this->addDataProvider($this->getName(), $this->wrappedElement);
    }

    /**
     * @param string $type
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render($type = Html::TYPE_HTML)
    {
        return $this->wrappedElement->toHtml();
    }

    /**
     * Call to wrapped element method
     *
     * @param $method
     * @param array $arguments
     */
    public function call($method, array $arguments)
    {
        if ($this->wrappedElement) {
            call_user_func_array(array($this->wrappedElement, $method), $arguments);
        }
    }

    /**
     * Retrieve wrapped element instance
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function getWrappedElement()
    {
        return $this->wrappedElement;
    }
}
