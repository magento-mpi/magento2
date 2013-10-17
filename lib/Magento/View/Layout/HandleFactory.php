<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout;

use Magento\ObjectManager;

use Magento\View\Layout\Handle;

class HandleFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Handle[]
     */
    protected $handles = array();

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return Handle
     */
    public function get($type)
    {
        $handles = array(
            'block' => 'Magento\View\Layout\Handle\Render\Block',
            'template' => 'Magento\View\Layout\Handle\Render\Template',
            'container' => 'Magento\View\Layout\Handle\Render\Container',
            'preset' => 'Magento\View\Layout\Handle\Render\Preset',

            'move' => 'Magento\View\Layout\Handle\Command\Move',
            'remove' => 'Magento\View\Layout\Handle\Command\Remove',
            'update' => 'Magento\View\Layout\Handle\Command\Update',
            'action' => 'Magento\View\Layout\Handle\Command\Action',

            'data' => 'Magento\View\Layout\Handle\Data\Source',

            'referenceBlock' => 'Magento\View\Layout\Handle\Reference\Block',
            'referenceContainer' => 'Magento\View\Layout\Handle\Reference\Container',

            'arguments' => 'Magento\View\Layout\Handle\Arguments',
        );

        if (!isset($this->handles[$type])) {
            $className = isset($handles[$type]) ? $handles[$type] : null;
            $this->handles[$type] = $this->objectManager->get($className);
        }

        return $this->handles[$type];
    }
}
