<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Render;

use Magento\Framework\ObjectManager;
use Magento\Framework\View\RenderInterface;

/**
 * Class RenderFactory
 */
class RenderFactory
{
    /**
     * Object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get method
     *
     * @param string $type
     * @return RenderInterface
     * @throws \InvalidArgumentException
     */
    public function get($type)
    {
        $className = 'Magento\\View\\Render\\' . ucfirst($type);
        $model = $this->objectManager->get($className);

        if ($model instanceof RenderInterface === false) {
            throw new \InvalidArgumentException(
                sprintf('Type "%s" is not instance on Magento\Framework\View\RenderInterface', $type)
            );
        }

        return $model;
    }
}
