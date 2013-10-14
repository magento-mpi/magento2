<?php

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
     * @param string $type
     * @param array $arguments [optional]
     * @return Render
     */
    public function get($type, array $arguments = array())
    {
        $className = 'Magento\\View\\Render\\' . ucfirst($type);

        return $this->objectManager->get($className, $arguments);
    }
}
