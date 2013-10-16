<?php

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;
use Magento\View\Context;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;
use Magento\View\Layout\Reader;

class Action extends Base implements ContainerInterface
{
    const TYPE = 'action';

    /**
     * Configuration path to check.
     *
     * @var string
     */
    protected $ifConfig;

    /**
     * Target element's methods to call.
     *
     * @var string
     */
    protected $method;

    /**
     * @param Context $context
     * @param RenderFactory $renderFactory
     * @param ViewFactory $viewFactory
     * @param ObjectManager $objectManager
     * @param Reader $layoutReader
     * @param ContainerInterface $parent
     * @param array $meta
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Reader $layoutReader,
        ContainerInterface $parent = null,
        array $meta = array()
    )
    {
        parent::__construct($context, $renderFactory, $viewFactory, $objectManager, $parent, $meta);

        $this->ifConfig = isset($meta['ifconfig']) ? $meta['ifconfig'] : null;
        $this->method = isset($meta['method']) ? $meta['method'] : null;
    }

    /**
     *
     */
    public function register(ContainerInterface $parent = null)
    {
        $children = isset($this->meta['children']) ? $this->meta['children'] : array();
        $arguments = array();
        foreach ($children as $child) {
            $arguments[$child['name']] = $child['value'];
        }

        if (method_exists($parent, 'call')) {
            $parent->call($this->method, $arguments);
        }
    }
}
