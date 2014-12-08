<?php
/**
 * Compiled method plugin definitions. Must be used in production for maximum performance
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Interception\Definition;

use Magento\Framework\Interception\DefinitionInterface;

class Compiled implements DefinitionInterface
{
    /**
     * List of plugin definitions
     *
     * @var array
     */
    protected $_definitions = [];

    /**
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->_definitions = $definitions;
    }

    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return string[]
     */
    public function getMethodList($type)
    {
        return $this->_definitions[$type];
    }
}
