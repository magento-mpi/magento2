<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\EntryPoint;

use Magento\App\Dir,
    Magento\App\State,
    Magento\App\EntryPointInterface,
    Magento\ObjectManager,
    Magento\App\ObjectManagerFactory;

class EntryPoint implements EntryPointInterface
{
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
     */
    public function __construct(
        $rootDir,
        array $parameters = array(),
        ObjectManager $objectManager = null
    ) {
        $this->_parameters = $parameters;
        if (!$this->_locator) {
            \Magento\Profiler::start('locator');
            try {
                $locatorFactory = new ObjectManagerFactory();
                $objectManager = $locatorFactory->create($rootDir, $parameters);
            } catch (\Exception $exception) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                if (isset($this->_parameters[state::PARAM_MODE])
                    && $this->_parameters[State::PARAM_MODE] == State::MODE_DEVELOPER
                ) {
                    print '<pre>';
                    print $exception->getMessage() . "\n\n";
                    print $exception->getTraceAsString();
                    print '</pre>';
                } else {
                    print "Exception happened during application bootstrap.";
                }
                exit;
            }
            \Magento\Profiler::stop('locator');
        }
        $this->_locator = $objectManager;
    }

    /**
     * Process exception
     *
     * @param \Exception $exception
     */
    protected function _processException(\Exception $exception)
    {
        if (isset($this->_parameters[state::PARAM_MODE])
            && $this->_parameters[State::PARAM_MODE] == State::MODE_DEVELOPER
        ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            print '<pre>';
            print $exception->getMessage() . "\n\n";
            print $exception->getTraceAsString();
            print '</pre>';
        } else {
            $reportData = array($exception->getMessage(), $exception->getTraceAsString());

            // retrieve server data
            if (isset($_SERVER)) {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $reportData['url'] = $_SERVER['REQUEST_URI'];
                }
                if (isset($_SERVER['SCRIPT_NAME'])) {
                    $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                }
            }

            try {
                $modelDir = $this->_locator->get('Magento\App\Dir');
                require_once ($modelDir->getDir(Dir::PUB) . DS . 'errors' . DS . 'report.php');
            } catch (\Exception $exception) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                echo "Unknown error happened.";
            }
        }
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
            return $this->_locator->create($applicationName, $arguments)->execute();
        } catch (\Exception $e) {
            $this->_processException($e);
            return -1;
        }
    }
}
