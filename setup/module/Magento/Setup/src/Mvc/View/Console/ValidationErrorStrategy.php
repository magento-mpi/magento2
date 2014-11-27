<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Mvc\View\Console;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ConsoleModel;
use Zend\Console\ColorInterface;

class ValidationErrorStrategy extends \Zend\Mvc\View\Console\RouteNotFoundStrategy
{
    /**
     * Error message
     *
     * @var string
     */
    private $errorMessage = '';

    /**
     * {@inheritdoc}
     */
    public function handleNotFoundError(MvcEvent $e)
    {
        $request = $e->getRequest();
        $serviceManager = $e->getApplication()->getServiceManager();
        $moduleManager = $serviceManager->get('ModuleManager');
        $console = $serviceManager->get('console');
        $scriptName = basename($request->getScriptName());

        $banner = $this->getConsoleBanner($console, $moduleManager);

        $usage = $this->getConsoleUsage($console, $scriptName, $moduleManager);

        $result  = $banner ? rtrim($banner, "\r\n")        : '';
        $result .= $usage  ? "\n\n" . trim($usage, "\r\n") : '';
        $result .= "\n";

        $this->errorMessage = $console->colorize($this->errorMessage, ColorInterface::RED);
        $result .= $this->errorMessage . "\n";

        $model = new ConsoleModel();
        $model->setErrorLevel(1);
        $model->setResult($result);

        $e->setResult($model);
    }

    /**
     * Set error message
     *
     * @param string $message
     */
    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }
}
