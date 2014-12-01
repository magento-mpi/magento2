<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Mvc;

use Magento\Setup\Model\Validator;
use Zend\View\Model\ConsoleModel;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Console\ColorInterface;

class ValidatingRouteListener extends \Zend\Mvc\RouteListener
{
    /**
     * {@inheritdoc}
     */
    public function onRoute($e)
    {
        $request = $e->getRequest();
        // propagates to default RouteListener if not CLI
        if (!$request instanceof \Zend\Console\Request) {
            return null;
        }

        $router = $e->getRouter();
        $match = $router->match($request);

        $validationMessages = '';
        // CLI routing miss, checks for missing/extra parameters
        if (!$match instanceof RouteMatch) {
            $validationMessages .= $this->checkParams($e);
            if ('' !== $validationMessages) {
                $this->displayMessage($e, $validationMessages);
                // set error to stop propagation
                $e->setError('default_error');
            }
        }
        return null;
    }

    /**
     * Checks parameters
     *
     * @param MvcEvent $e
     * @return string
     */
    private function checkParams(MvcEvent $e)
    {
        $request = $e->getRequest();
        $content = $request->getContent();
        $serviceManager = $e->getApplication()->getServiceManager();

        $userAction = $content[0];
        array_shift($content);

        $routes = $serviceManager->get('Config')['console']['router']['routes'];
        $validationMessages = '';
        if (isset($routes[$userAction])) {
            // parse the expected parameters of the action
            $matcher = new \Magento\Setup\Model\RouteMatcher($routes[$userAction]['options']['route']);
            $parts = $matcher->getParts();
            array_shift($parts);
            $expectedParams = [];
            foreach ($parts as $part) {
                $expectedParams[$part['name']] = $part;
            }
            // parse user parameters
            $userParams = $this->parseUserParams($content);

            $validator = new Validator();
            $missingParams = $validator->checkMissingParameter($expectedParams, $userParams);
            $extraParams = $validator->checkExtraParameter($expectedParams, $userParams);
            $missingValues = $validator->checkMissingValue($expectedParams, $userParams);
            $extraValues = $validator->checkExtraValue($expectedParams, $userParams);

            if (!empty($missingParams)) {
                $validationMessages .= 'Missing parameters:' . PHP_EOL;
                foreach ($missingParams as $missingParam) {
                    $validationMessages .= $missingParam . PHP_EOL;
                }
                $validationMessages .= PHP_EOL;
            }
            if (!empty($extraParams)) {
                $validationMessages .= 'Unidentified parameters:' . PHP_EOL;
                foreach ($extraParams as $extraParam) {
                    $validationMessages .= $extraParam . PHP_EOL;
                }
                $validationMessages .= PHP_EOL;
            }
            if (!empty($missingValues)) {
                $validationMessages .= 'Parameters missing value:' . PHP_EOL;
                foreach ($missingValues as $missingValue) {
                    $validationMessages .= $missingValue . PHP_EOL;
                }
                $validationMessages .= PHP_EOL;
            }
            if (!empty($extraValues)) {
                $validationMessages .= 'Parameters that don\'t need value:' . PHP_EOL;
                foreach ($extraValues as $extraValue) {
                    $validationMessages .= $extraValue . PHP_EOL;
                }
                $validationMessages .= PHP_EOL;
            }
            if (empty($missingParams) && empty($extraParams) && empty($missingValues) && empty($extraValue)) {
                $validationMessages .= 'Please make sure parameters start with --.' . PHP_EOL;
                $validationMessages .= PHP_EOL;
            }

        } else if (!is_null($userAction)) {
            $validationMessages .= "Unknown action name '{$userAction}'." . PHP_EOL;
            $validationMessages .= PHP_EOL;
        }

        return $validationMessages;
    }

    private function displayMessage(MvcEvent $e, $validationMessages)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $console = $serviceManager->get('console');
        $validationMessages = $console->colorize($validationMessages, ColorInterface::RED);
        $model = new ConsoleModel();
        $model->setErrorLevel(1);
        $model->setResult($validationMessages);
        $e->setResult($model);
    }

    /**
     * Parse user input
     *
     * @param array $content
     * @return array
     */
    private function parseUserParams(array $content)
    {
        $parameters = [];
        foreach ($content as $param) {
            $parsed = explode('=', $param, 2);
            $value = isset($parsed[1]) ? $parsed[1] : '';
            if (strpos($parsed[0], '--') !== false) {
                $key = substr($parsed[0], 2, strlen($parsed[0]) - 2);
            } else {
                $key = $parsed[0];
            }

            $parameters[$key] = $value;
        }
        return $parameters;
    }
}
