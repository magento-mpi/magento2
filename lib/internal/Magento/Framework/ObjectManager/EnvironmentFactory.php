<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager;

use Magento\Framework\App\Arguments;
use Magento\Framework\ObjectManager\Environment\Compiled;
use Magento\Framework\ObjectManager\Environment\Developer;

class EnvironmentFactory
{
    /**
     * @var RelationsInterface
     */
    private $relations;

    /**
     * @var DefinitionInterface
     */
    private $definitions;

    /**
     * @param RelationsInterface $relations
     * @param DefinitionInterface $definitions
     */
    public function __construct(
        RelationsInterface $relations,
        DefinitionInterface $definitions
    ) {
        $this->relations = $relations;
        $this->definitions = $definitions;
    }

    /**
     * Create Environment object
     *
     * @return EnvironmentInterface
     */
    public function createEnvironment()
    {
        switch ($this->getMode()) {
            case Compiled::MODE:
                return new Compiled($this);
                break;
            default:
                return new Developer($this);
        }
    }

    /**
     * Determinate running mode
     *
     * @return string
     */
    private function getMode()
    {
        if (file_exists(Compiled::getFilePath())) {
            return Compiled::MODE;
        }

        return Developer::MODE;
    }

    /**
     * @return DefinitionInterface
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @return RelationsInterface
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
