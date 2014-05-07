<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\EntryPoint;

use Mtf\ObjectManager;

/**
 * Class EntryPoint
 * Application entry point, used to bootstrap and run application
 *
 * @package Mtf\EntryPoint
 */
class EntryPoint
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
     * Run a Mtf application
     *
     * @param $applicationName
     * @param array $arguments
     * @return mixed
     * @throws \DomainException
     */
    public function run($applicationName, array $arguments = array())
    {
        try {
            if (!$this->_locator) {
                $locatorFactory = new \Mtf\ObjectManagerFactory();
                $this->_locator = $locatorFactory->create();
            }
            return $this->_locator->create($applicationName, $arguments)->launch();
        } catch (\Exception $exception) {
            echo $exception->getTraceAsString();
            $message = "Error happened during application run.\n";
            $message .= $exception->getMessage();
            throw new \DomainException($message);
        }
    }
}
