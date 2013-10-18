<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Render;

use Magento\ObjectManager;
use Magento\View\Render;

class RenderFactory
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
     * @param $type
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($type)
    {
        $className = 'Magento\\View\\Render\\' . ucfirst($type);
        $model = $this->objectManager->get($className);

        if (($model instanceof Render) === false) {
            throw new \InvalidArgumentException(sprintf('Type "%s" is not instance on Magento\View\Render', $type));
        }

        return $model;
    }
}
