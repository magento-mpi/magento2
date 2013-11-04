<?php
/**
 * Application entry point, used to bootstrap and run application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\EntryPoint;

use Magento\App\State,
    Magento\App\EntryPointInterface,
    Magento\ObjectManager;

class EntryPoint implements EntryPointInterface
{
    /**
     * @var string
     */
    protected $_rootDir;

    /**
     * @var array
     */
    protected $_parameters;

    /**
     * Application object manager
     *
     * @var ObjectManager
     */
    protected $_locator;

    /**
     * @param string $rootDir
     * @param array $parameters
     * @param ObjectManager $objectManager
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function __construct(
        $rootDir,
        array $parameters = array(),
        ObjectManager $objectManager = null
    ) {
        $this->_rootDir = $rootDir;
        $this->_parameters = $parameters;
        $this->_locator = $objectManager;
    }

    /**
     * Run application
     *
     * @param string $applicationName
     * @param array $arguments
     * @return int
     */
    public function run($applicationName, array $arguments = array())
    {
        try {
            if (!$this->_locator) {
                $locatorFactory = new \Magento\App\ObjectManagerFactory();
                $this->_locator = $locatorFactory->create($this->_rootDir, $this->_parameters);
            }
            return $this->_locator->create($applicationName, $arguments)->execute();
        } catch (\Exception $exception) {
            if (isset($this->_parameters[state::PARAM_MODE])
                && $this->_parameters[State::PARAM_MODE] == State::MODE_DEVELOPER
            ) {
                print $exception->getMessage() . "\n\n";
                print $exception->getTraceAsString();
            } else {
                print "Unknown error happened during application run\n";
            }
            return 1;
        }
    }
}
