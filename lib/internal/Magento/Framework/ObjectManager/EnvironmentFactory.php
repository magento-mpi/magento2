<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager;

use Magento\Framework\App\Arguments;
use Magento\Framework\ObjectManager\Definition;
use Magento\Framework\ObjectManager\Environment\Compiled;
use Magento\Framework\ObjectManager\Environment\Developer;
use Magento\Framework\ObjectManager\Relations;

class EnvironmentFactory
{
    /**
     * @var Relations
     */
    private $relations;

    /**
     * @var Definition
     */
    private $definitions;

    /**
     * @var Arguments
     */
    private $appArguments;

    /**
     * @param Relations $relations
     * @param Definition $definitions
     * @param Arguments $appArguments
     */
    public function __construct(Relations $relations, Definition $definitions, Arguments $appArguments)
    {
        $this->relations = $relations;
        $this->definitions = $definitions;
        $this->appArguments = $appArguments;
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
     * @return Arguments
     */
    public function getAppArguments()
    {
        return $this->appArguments;
    }

    /**
     * @return Definition
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @return Relations
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
