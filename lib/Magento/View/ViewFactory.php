<?php

namespace Magento\View;

use Magento\ObjectManager;
use Magento\App\Context;

use Magento\View\Element;
use Magento\View\Element\Page;
use Magento\View\Element\Page\Link;
use Magento\View\Element\Page\Meta;
use Magento\View\Element\Page\Script;
use Magento\View\Element\Page\Style;
use Magento\View\Element\Page\Title;
use Magento\View\Element\Block;
use Magento\View\Element\Container;
use Magento\View\Element\Data;
use Magento\View\Element\Handle;
use Magento\View\Element\Template;

class ViewFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Page
     */
    public function createPage(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Page',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Block
     */
    public function createBlock(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Block',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Container
     */
    public function createContainer(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Container',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Data
     */
    public function createDataProvider(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Data',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Handle
     */
    public function createHandle(Context $context, array $meta = array())
    {
        $handle = $this->objectManager->create('Magento\\View\\Element\\Handle',
            array('context' => $context, 'meta' => $meta));

        return $handle;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Template
     */
    public function createTemplate(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Template',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Title
     */
    public function createPageTitle(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Page\\Title',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Link
     */
    public function createPageLink(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Page\\Link',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Script
     */
    public function createPageScript(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Page\\Script',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Style
     */
    public function createPageStyle(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Page\\Style',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Meta
     */
    public function createPageMeta(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create('Magento\\View\\Element\\Page\\Meta',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return Element
     */
    public function create($type, array $arguments)
    {
        $className = 'Magento\\View\\Element\\' . ucfirst(str_replace('_', '\\', $type));

        return $this->objectManager->create($className, $arguments);
    }
}
