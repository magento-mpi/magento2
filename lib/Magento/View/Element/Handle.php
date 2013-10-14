<?php

namespace Magento\View\Element;

use Magento\View\Element;
use Magento\App\Context;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;
use Magento\View\Layout\Reader;

class Handle extends Base implements Element
{
    const TYPE = 'handle';

    /**
     * @var Reader
     */
    protected $layoutReader;

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

        $this->layoutReader = $layoutReader;
    }

    public function register(Element $parent = null)
    {
        if (isset($parent)) {
            $this->layoutReader->loadHandle($this->meta['handle'], $parent);
        }
    }
}
