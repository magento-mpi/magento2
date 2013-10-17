<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Container\Render;

use Magento\View\Container as ContainerInterface;
use Magento\View\Render\Html;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\View\Context ;
use Magento\ObjectManager;

class Page extends Base implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = 'page';

    /**
     * @param Context $context
     * @param RenderFactory $renderFactory
     * @param ViewFactory $viewFactory
     * @param ObjectManager $objectManager
     * @param ContainerInterface $parent [optional]
     * @param array $meta [optional]
     * @throws \InvalidArgumentException
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

        $this->addHandle('default');
        $this->addHandle($context->getFullActionName());
    }

    /**
     * @param ContainerInterface $parent
     */
    public function register(ContainerInterface $parent = null)
    {
        if (isset($parent)) {
            $parent->attach($this, $this->alias, $this->before, $this->after);
        }

        foreach ($this->getChildren() as $child) {
            $metaElement = $this->viewFactory->create(
                $child['type'],
                array(
                    'context' => $this->context,
                    'parent' => $this,
                    'meta' => $child,
                )
            );
            $metaElement->register($this);
        }
    }

    /**
     * @param string $type
     * @return string
     */
    public function render($type = Html::TYPE_HTML)
    {
        $result = '';
        foreach ($this->getChildrenElements() as $child) {
            /** @var $child ContainerInterface */
            $result .= $child->render($type);
        }

        $render = $this->renderFactory->get($type);
        return $render->renderContainer($result);
    }
}
