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
use Magento\Framework\Module\DbVersionDetector;

class DbStatusValidator
{
    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var DbVersionDetector
     */
    private $dbVersionDetector;

    /**
     * @param FrontendInterface $cache
     * @param DbVersionDetector $dbVersionDetector
     */
    public function __construct(
        FrontendInterface $cache,
        DbVersionDetector $dbVersionDetector
    ) {
        $this->cache = $cache;
        $this->dbVersionDetector = $dbVersionDetector;
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
            $errors = $this->dbVersionDetector->getDbVersionErrors();
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
            $formattedErrors[] = $error[DbVersionDetector::ERROR_KEY_MODULE] .
                ' ' . $error[DbVersionDetector::ERROR_KEY_TYPE] .
                ': current version - ' . $error[DbVersionDetector::ERROR_KEY_CURRENT ] .
                ', latest version - ' . $error[DbVersionDetector::ERROR_KEY_NEEDED];
        }
        return $formattedErrors;
    }

}
