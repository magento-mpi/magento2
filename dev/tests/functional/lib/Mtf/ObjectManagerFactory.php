<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\BooleanUtils;
use Mtf\System\Config as SystemConfig;
use Mtf\ObjectManager\Factory;
use Magento\Framework\ObjectManagerInterface as MagentoObjectManager;

/**
 * Class ObjectManagerFactory
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        if (!defined('MTF_STATES_PATH')) {
            define('MTF_STATES_PATH', MTF_BP . '/lib/Mtf/App/State/');
        }

        $diConfig = new $this->configClassName();
        $systemConfig = new SystemConfig();
        $configuration = $systemConfig->getConfigParam();
        $diConfig->extend($configuration);

        $directories = isset($arguments[Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS])
            ? $arguments[Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS]
            : array();
        $directoryList = new \Magento\Framework\App\Filesystem\DirectoryList(
            realpath(MTF_BP . '../../../../'),
            $directories
        );
        (new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
            array($directoryList->getPath(DirectoryList::GENERATION))
        );

        $factory = new Factory($diConfig);
        $argInterpreter = $this->createArgumentInterpreter(new BooleanUtils());
        $argumentMapper = new \Magento\Framework\ObjectManager\Config\Mapper\Dom($argInterpreter);

        $sharedInstances['Magento\Framework\ObjectManager\Config\Mapper\Dom'] = $argumentMapper;
        $objectManager = new $this->locatorClassName($factory, $diConfig, $sharedInstances);

        $factory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        self::configure($objectManager);

        return $objectManager;
    }

    /**
     * Create instance of application arguments
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param array $arguments
     * @return \Magento\Framework\App\Arguments
     */
    protected function createAppArguments(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        array $arguments
    ) {
        return new \Magento\Framework\App\Arguments(
            $arguments,
            new \Magento\Framework\App\Arguments\Loader(
                $directoryList,
                isset($arguments[\Magento\Framework\App\Arguments\Loader::PARAM_CUSTOM_FILE])
                ? $arguments[\Magento\Framework\App\Arguments\Loader::PARAM_CUSTOM_FILE]
                : null
            )
        );
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @param \Magento\Framework\Stdlib\BooleanUtils $booleanUtils
     * @return \Magento\Framework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter(
        \Magento\Framework\Stdlib\BooleanUtils $booleanUtils
    ) {
        $constInterpreter = new \Magento\Framework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\Framework\Data\Argument\Interpreter\Composite(
            array(
                'boolean' => new \Magento\Framework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\Framework\Data\Argument\Interpreter\String($booleanUtils),
                'number' => new \Magento\Framework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Framework\Data\Argument\Interpreter\NullType(),
                'const' => $constInterpreter,
                'object' => new \Magento\Framework\Data\Argument\Interpreter\Object($booleanUtils),
                'init_parameter' => new \Magento\Framework\App\Arguments\ArgumentInterpreter($constInterpreter),
            ),
            \Magento\Framework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\Framework\Data\Argument\Interpreter\ArrayType($result));
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
            $objectManager->get('Mtf\ObjectManager\ConfigLoader\Module')->load('etc/ui')
        );

        $objectManager->configure(
            $objectManager->get('Mtf\ObjectManager\ConfigLoader\Module')->load('etc/curl')
        );
    }
}
