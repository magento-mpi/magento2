<?php

namespace Magento\View\Element;

use Magento\ObjectManager;
use Magento\View\Element;
use Magento\View\Render\Html;
use Magento\View\ViewFactory;
use Magento\App\Context;
use Magento\View\Render\RenderFactory;

class Container extends Base implements Element
{
    const TYPE = 'container';

    /**#@+
     * Names of container options in layout
     */
    const CONTAINER_OPT_HTML_TAG = 'htmlTag';
    const CONTAINER_OPT_HTML_CLASS = 'htmlClass';
    const CONTAINER_OPT_HTML_ID = 'htmlId';
    const CONTAINER_OPT_LABEL = 'label';
    /**#@-*/

    /**
     * @var array
     */
    protected $containerInfo = array();

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

        $this->containerInfo['label'] = isset($meta['label']) ? $meta['label'] : null;
        $this->containerInfo['tag'] = isset($meta['htmlTag']) ? $meta['htmlTag'] : null;
        $this->containerInfo['class'] = isset($meta['htmlClass']) ? $meta['htmlClass'] : null;
        $this->containerInfo['id'] = isset($meta['htmlId']) ? $meta['htmlId'] : null;

        if (empty($this->containerInfo['tag']) && (!empty($this->containerInfo['class']) || !empty($this->containerInfo['id']))) {
            throw new \InvalidArgumentException('HTML ID or class will not have effect, if HTML tag is not specified.');
        }

        // share parent data with nested elements
        if (isset($this->parent)) {
            $this->dataProviders = & $this->parent->getDataProviders();
        }
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
        $result = $render->renderContainer($result, $this->containerInfo);

        return $result;
    }
}
