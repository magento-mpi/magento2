<?php
/**
 * Validation of DB up to date state
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Plugin;

use Magento\Framework\Cache\FrontendInterface;

class DbStatusValidator
{
    /**#@+
     * Constants defined for keys of error array
     */
    const ERROR_KEY_MODULE = 'module';
    const ERROR_KEY_TYPE = 'type';
    const ERROR_KEY_CURRENT = 'current';
    const ERROR_KEY_NEEDED = 'needed';
    /**#@-*/

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\Module\ResourceResolverInterface
     */
    private $resourceResolver;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @param FrontendInterface $cache
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\ResourceResolverInterface $resourceResolver
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        FrontendInterface $cache,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\ResourceResolverInterface $resourceResolver,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->cache = $cache;
        $this->moduleList = $moduleList;
        $this->resourceResolver = $resourceResolver;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\App\FrontController $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @throws \Magento\Framework\Module\Exception
     * @return \Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Framework\App\FrontController $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (!$this->cache->load('db_is_up_to_date')) {
            $errors  = $this->getOutOfDateDbErrors();
            if ($errors) {
                $formattedErrors = $this->formatErrors($errors);
                throw new \Magento\Framework\Module\Exception(
                    'Please update your database: first run "composer install" from the Magento root/ and root/setup '.
                    'directories. Then run "php –f index.php update" from the Magento root/setup directory.'. PHP_EOL .
                    'Error details: database is out of date.' . PHP_EOL . implode(PHP_EOL, $formattedErrors)
                );
            } else {
                $this->cache->save('true', 'db_is_up_to_date');
            }
        }
        return $proceed($request);
    }

    /**
     * Format each error in the error data from getOutOfDataDbErrors into a single message
     *
     * @param $errorsData array of error data from getOutOfDateDbErrors
     * @return array Messages that can be used to log the error
     */
    private function formatErrors($errorsData)
    {
        $formattedErrors = [];
        foreach ($errorsData as $error) {
            $formattedErrors[] = $error[self::ERROR_KEY_MODULE] . ' ' . $error[self::ERROR_KEY_TYPE] .
                ': current version - ' . $error[self::ERROR_KEY_CURRENT ] .
                ', latest version - ' . $error[self::ERROR_KEY_NEEDED];
        }
        return $formattedErrors;
    }

    /**
     * Get array of errors if DB is out of date, return [] if DB is current
     *
     * @return [] Array of errors, each error contains module name, current version, needed version,
     *              and type (schema or data).  The array will be empty if all schema and data are current.
     */
    private function getOutOfDateDbErrors()
    {
        $errors = [];
        foreach (array_keys($this->moduleList->getModules()) as $moduleName) {
            foreach ($this->resourceResolver->getResourceList($moduleName) as $resourceName) {
                $errorData = $this->moduleManager->getDbSchemaVersionError($moduleName, $resourceName);
                if ($errorData) {
                    $errors[] = array_merge(
                        [self::ERROR_KEY_MODULE => $moduleName, '' . self::ERROR_KEY_TYPE . '' => 'schema'],
                        $errorData
                    );
                }
                $errorData = $this->moduleManager->getDbDataVersionError($moduleName, $resourceName);
                if ($errorData) {
                    $errors[] = array_merge(
                        [self::ERROR_KEY_MODULE => $moduleName, self::ERROR_KEY_TYPE => 'data'],
                        $errorData
                    );
                }
            }
        }
        return $errors;
    }
}
