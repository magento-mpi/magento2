<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_View
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Page View.
 *
 * @category    Magento
 * @package     Magento_View
 */

namespace Magento\View\Element;

use Magento\View\Element;
use Magento\View\Element\Container;
use Magento\View\Render\Html;
use Magento\View\Render\RenderFactory;
use Magento\View\ViewFactory;
use Magento\App\Context ;
use Magento\ObjectManager;

class Page extends Base implements Element
{
    const TYPE = 'page';

    /**
     * @param Context $context
     * @param RenderFactory $renderFactory
     * @param ViewFactory $viewFactory
     * @param ObjectManager $objectManager
     * @param Element $parent [optional]
     * @param array $meta [optional]
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        Element $parent = null,
        array $meta = array()
    )
    {
        parent::__construct($context, $renderFactory, $viewFactory, $objectManager, $parent, $meta);

        $this->addHandle('default');
        $this->addHandle($context->getFullActionName());
    }

    public function register(Element $parent = null)
    {
        if (isset($parent)) {
            $parent->attach($this, $this->alias, $this->before, $this->after);
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
    }

    public function render($type = Html::TYPE_HTML)
    {
        $result = '';
        foreach ($this->getChildrenElements() as $child) {
            //var_dump(get_class($child) . ' -- ' . $child->getName());
            /** @var $element Element */
            $result .= $child->render($type);
        }

        $render = $this->renderFactory->get($type);
        $result = $render->renderContainer($result);

        return $result;
    }
}
