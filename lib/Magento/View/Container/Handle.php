<?php

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;
use Magento\View\Context;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;
use Magento\View\Layout\Reader;

class Handle extends Base implements ContainerInterface
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

        $this->layoutReader = $layoutReader;
    }

    public function register(ContainerInterface $parent = null)
    {
        if (isset($parent)) {
            $this->layoutReader->loadHandle($this->meta['handle'], $parent);
        }
    }
}
