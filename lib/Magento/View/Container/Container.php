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
use Magento\View\Render\Html;
use Magento\View\ViewFactory;
use Magento\View\Context;
use Magento\View\Render\RenderFactory;

class Container extends Base implements ContainerInterface
{
    /**
     * Container type
     */
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
     * @param ContainerInterface $parent [optional]
     * @param array $meta [optional]
     * @throws \InvalidArgumentException
     * @todo Reduce NPathComplexity
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
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

        $this->containerInfo['label'] = isset($this->meta['label']) ? $this->meta['label'] : null;
        $this->containerInfo['tag'] = isset($this->meta['htmlTag']) ? $this->meta['htmlTag'] : null;
        $this->containerInfo['class'] = isset($this->meta['htmlClass']) ? $this->meta['htmlClass'] : null;
        $this->containerInfo['id'] = isset($this->meta['htmlId']) ? $this->meta['htmlId'] : null;

        if (empty($this->containerInfo['tag'])
            && (!empty($this->containerInfo['class']) || !empty($this->containerInfo['id']))) {
            throw new \InvalidArgumentException('HTML ID or class will not have effect, if HTML tag is not specified.');
        }

        // Share parent data with nested elements
        if (isset($this->parent)) {
            $this->dataProviders = & $this->parent->getDataProviders();
        }
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
        return $render->renderContainer($result, $this->containerInfo);
    }
}
