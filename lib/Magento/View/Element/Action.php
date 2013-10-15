<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Element;

use Magento\View\Element;
use Magento\View\Context;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\ObjectManager;

class Action extends Base implements Element
{
    /**
     * Element type
     */
    const TYPE = 'action';

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
     * @param Element $parent
     * @param array $meta
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Element $parent = null,
        array $meta = array()
    ) {
        parent::__construct($context, $renderFactory, $viewFactory, $objectManager, $parent, $meta);

        $this->method = isset($this->meta['method']) ? $this->meta['method'] : null;
    }

    /**
     * @param Element $parent
     */
    public function register(Element $parent = null)
    {
        $arguments = array();
        foreach ($this->getChildren() as $child) {
            $arguments[$child['name']] = $child['value'];
        }

        if (method_exists($parent, 'call')) {
            $parent->call($this->method, $arguments);
        }
    }
}
