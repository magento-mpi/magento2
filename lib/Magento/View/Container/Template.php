<?php

namespace Magento\View\Container;

use Magento\ObjectManager;
use Magento\View\Container as ContainerInterface;
use Magento\View\Render\Html;
use Magento\View\ViewFactory;
use Magento\View\Context;
use Magento\View\Render\RenderFactory;
use Magento\Core\Model\View\FileSystem;

class Template extends Base implements ContainerInterface
{
    const TYPE = 'template';

    /**
     * @var FileSystem
     */
    protected $filesystem;

    /**
     * @param Context $context
     * @param RenderFactory $renderFactory
     * @param ViewFactory $viewFactory
     * @param ObjectManager $objectManager
     * @param FileSystem $filesystem
     * @param ContainerInterface $parent
     * @param array $meta
     */
    public function __construct(
        Context $context,
        RenderFactory $renderFactory,
        ViewFactory $viewFactory,
        ObjectManager $objectManager,
        FileSystem $filesystem,
        ContainerInterface $parent = null,
        array $meta = array()
        )
    {
        parent::__construct($context, $renderFactory, $viewFactory, $objectManager, $parent, $meta);

        $this->filesystem = $filesystem;
    }

    public function register(ContainerInterface $parent = null)
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

    /**
     * @param string $type
     * @return mixed
     */
    public function render($type = Html::TYPE_HTML)
    {
        $render = $this->renderFactory->get($type);

        $dataProviders = $this->getDataProviders();
        // TODO probably prepare limited proxy to avoid violations
        $dataProviders['view'] = $this;

        $result = $render->renderTemplate($this->getTemplateFile(), $dataProviders);

        return $result;
    }

    /**
     * Get absolute path to template
     *
     * @return string
     */
    protected function getTemplateFile()
    {
        // TODO rid of using area
        $this->meta['area'] = $this->context->getArea();
        $templateName = $this->filesystem->getFilename($this->meta['path'], $this->meta);

        return $templateName;
    }
}
