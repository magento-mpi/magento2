<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

use Mtf\System\Config as SystemConfig;
use Mtf\ObjectManager\Factory;
use Magento\ObjectManager as MagentoObjectManager;

/**
 * Class ObjectManagerFactory
 *
 * @package Mtf\System
 * @api
 */
class ObjectManagerFactory
{
    /**
     * Object Manager class name
     *
     * @var string
     */
    protected $locatorClassName = '\Mtf\ObjectManager';

    /**
     * DI Config class name
     *
     * @var string
     */
    protected $configClassName = '\Mtf\ObjectManager\Config';

    /**
     * Create Object Manager
     *
     * @param array $sharedInstances
     * @return ObjectManager
     */
    public function create(array $sharedInstances = [])
    {
        if (!defined('MTF_BP')) {
            $basePath = str_replace('\\', '/', dirname(dirname(__DIR__)));
            define('MTF_BP', $basePath);
        }
        if (!defined('MTF_TESTS_PATH')) {
            define('MTF_TESTS_PATH', MTF_BP . '/tests/app/');
        }

        $diConfig = new $this->configClassName();
        $systemConfig = new SystemConfig();
        $configuration = $systemConfig->getConfigParam();
        $diConfig->extend($configuration);

        $booleanUtils = new \Magento\Stdlib\BooleanUtils();
        $argFactory = new \Magento\ObjectManager\Config\Argument\ObjectFactory($diConfig);

        $directories = isset($arguments[\Magento\App\Filesystem::PARAM_APP_DIRS])
            ? $arguments[\Magento\App\Filesystem::PARAM_APP_DIRS]
            : array();
        $directoryList = new \Magento\App\Filesystem\DirectoryList(realpath(MTF_BP . '../../../../'), $directories);
        \Magento\Autoload\IncludePath::addIncludePath(
            array($directoryList->getDir(\Magento\App\Filesystem::GENERATION_DIR))
        );

        $appArguments = $this->createAppArguments($directoryList, []);
        $argInterpreter = $this->createArgumentInterpreter($booleanUtils, $argFactory, $appArguments);
        $factory = new Factory($diConfig, $argInterpreter, $argFactory);

        $objectManager = new $this->locatorClassName($factory, $diConfig, $sharedInstances);

        $argFactory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        self::configure($objectManager);

        return $objectManager;
    }

    /**
     * Create instance of application arguments
     *
     * @param \Magento\App\Filesystem\DirectoryList $directoryList
     * @param array $arguments
     * @return \Magento\App\Arguments
     */
    protected function createAppArguments(\Magento\App\Filesystem\DirectoryList $directoryList, array $arguments)
    {
        return new \Magento\App\Arguments(
            $arguments,
            new \Magento\App\Arguments\Loader(
                $directoryList,
                isset($arguments[\Magento\App\Arguments\Loader::PARAM_CUSTOM_FILE])
                    ? $arguments[\Magento\App\Arguments\Loader::PARAM_CUSTOM_FILE]
                    : null
            )
        );
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @param \Magento\Stdlib\BooleanUtils $booleanUtils
     * @param \Magento\ObjectManager\Config\Argument\ObjectFactory $objFactory
     * @param \Magento\App\Arguments $appArguments
     * @return \Magento\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter(
        \Magento\Stdlib\BooleanUtils $booleanUtils,
        \Magento\ObjectManager\Config\Argument\ObjectFactory $objFactory,
        \Magento\App\Arguments $appArguments
    ) {
        $constInterpreter = new \Magento\Data\Argument\Interpreter\Constant();
        $result = new \Magento\Data\Argument\Interpreter\Composite(
            array(
                'boolean' => new \Magento\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\Data\Argument\Interpreter\String($booleanUtils),
                'number' => new \Magento\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Data\Argument\Interpreter\NullType(),
                'const' => $constInterpreter,
                'object' => new \Magento\ObjectManager\Config\Argument\Interpreter\Object($booleanUtils, $objFactory),
                'init_parameter' => new \Magento\App\Arguments\ArgumentInterpreter($appArguments, $constInterpreter),
            ),
            \Magento\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }

    /**
     * Get MTF Object Manager instance
     *
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        if (!$objectManager = ObjectManager::getInstance()) {
            $objectManagerFactory = new self();
            $objectManager = $objectManagerFactory->create();
        }

        return $objectManager;
    }

    /**
     * Configure Object Manager
     * This method is static to have the ability to configure multiple instances of Object manager when needed
     *
     * @param MagentoObjectManager $objectManager
     */
    public static function configure(MagentoObjectManager $objectManager)
    {
        $objectManager->configure(
            $objectManager->get('Mtf\ObjectManager\ConfigLoader\Primary')->load()
        );

        $objectManager->configure(
            $objectManager->get('Mtf\ObjectManager\ConfigLoader\Module')->load()
        );

        $objectManager->configure(
            $objectManager->get('Mtf\ObjectManager\ConfigLoader\Module')->load('ui')
        );

        $objectManager->configure(
            $objectManager->get('Mtf\ObjectManager\ConfigLoader\Module')->load('curl')
        );
    }
}
