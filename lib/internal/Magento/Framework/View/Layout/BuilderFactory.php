<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

use Magento\Framework\ObjectManager;
use Magento\Framework\View;

/**
 * Class BuilderFactory
 */
class BuilderFactory
{
    /**#@+
     * Allowed builder types
     */
    const TYPE_LAYOUT = 'layout';
    const TYPE_PAGE   = 'page';
    /**#@-*/

    /**
     * Map of types which are references to classes
     *
     * @var array
     */
    protected $typeMap = [
        self::TYPE_LAYOUT => 'Magento\Framework\View\Layout\Builder',
        self::TYPE_PAGE   => 'Magento\Framework\View\Page\Builder',
    ];

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param array $types
     */
    public function __construct(
        ObjectManager $objectManager,
        array $types = []
    ) {
        $this->objectManager = $objectManager;
        $this->typeMap += $types;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return BuilderInterface
     */
    public function create($type, array $arguments)
    {
        if (empty($this->typeMap[$type])) {
            throw new \InvalidArgumentException('"' . $type . ': isn\'t allowed');
        }

        $builderInstance = $this->objectManager->create($this->typeMap[$type], $arguments);
        if (!$builderInstance instanceof BuilderInterface) {
            throw new \InvalidArgumentException(get_class($builderInstance) . ' isn\'t instance of BuilderInterface');
        }
        return $builderInstance;
    }
}
