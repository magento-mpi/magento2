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
use Magento\View\Layout\Handle;

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
     * @return Handle\Render\Block
     */
    public function createBlock(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create(
            'Magento\\View\\Layout\\Handle\\Render\\Block',
            array(
                'context' => $context,
                'meta' => $meta,
            )
        );
        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Handle\Render\Container
     */
    public function createContainer(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create(
            'Magento\\View\\Layout\\Handle\\Render\\Container',
            array(
                'context' => $context,
                'meta' => $meta,
            )
        );
        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Handle\Data\Source
     */
    public function createDataProvider(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create(
            'Magento\\View\\Layout\\Handle\\Data\\Source',
            array(
                'context' => $context,
                'meta' => $meta,
            )
        );
        return $view;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Handle\Render\Preset
     */
    public function createPreset(Context $context, array $meta = array())
    {
        $handle = $this->objectManager->create(
            'Magento\\View\\Layout\\Handle\\Render\\Preset',
            array(
                'context' => $context,
                'meta' => $meta,
            )
        );
        return $handle;
    }

    /**
     * @param Context $context
     * @param array $meta
     * @return Handle\Render\Template
     */
    public function createTemplate(Context $context, array $meta = array())
    {
        $view = $this->objectManager->create(
            'Magento\\View\\Layout\\Handle\\Render\\Template',
            array(
                'context' => $context,
                'meta' => $meta,
            )
        );
        return $view;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return ContainerInterface
     * @throws \InvalidArgumentException
     * @todo remove or update
     */
    public function create($type, array $arguments)
    {
        $className = 'Magento\\View\\Container\\' . ucfirst(str_replace('_', '\\', $type));
        $model = $this->objectManager->create($className, $arguments);

        if (($model instanceof ContainerInterface) === false) {
            throw new \InvalidArgumentException(sprintf('Type "%s" is not instance on Magento\View\Container', $type));
        }

        return $model;
    }
}
