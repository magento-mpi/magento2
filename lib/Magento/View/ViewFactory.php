<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\ObjectManager;
use Magento\View\Context;

use Magento\View\Container as ContainerInterface;
use Magento\View\Container\Page;
use Magento\View\Container\Block;
use Magento\View\Container\Data;
use Magento\View\Container\Handle;
use Magento\View\Container\Template;

class ViewFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
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
        $view = $this->objectManager->create('Magento\\View\\Container\\Page',
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
        $view = $this->objectManager->create('Magento\\View\\Container\\Block',
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
        $view = $this->objectManager->create('Magento\\View\\Container\\Container',
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
        $view = $this->objectManager->create('Magento\\View\\Container\\Data',
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
        $handle = $this->objectManager->create('Magento\\View\\Container\\Handle',
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
        $view = $this->objectManager->create('Magento\\View\\Container\\Template',
            array('context' => $context, 'meta' => $meta));

        return $view;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return ContainerInterface
     */
    public function create($type, array $arguments)
    {
        $className = 'Magento\\View\\Container\\' . ucfirst(str_replace('_', '\\', $type));
        $element = $this->objectManager->create($className, $arguments);

        if (($element instanceof ContainerInterface) === false) {
            throw new \InvalidArgumentException(sprintf('Type "%s" is not instance on Magento\View\Container', $type));
        }

        return $element;
    }
}
