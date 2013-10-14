<?php

namespace Magento\View\Element;

use Magento\View\Element;
use Magento\App\Context;
use Magento\View\Render\Html;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;
use Magento\View\Layout\Reader;

class Action extends Base implements Element
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
     * @param Element $parent
     * @param array $meta
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Reader $layoutReader,
        Element $parent = null,
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
    public function register(Element $parent = null)
    {
        if (method_exists($parent, 'call')) {
            $parent->call($this->method, $this->arguments);
        }
    }
}
