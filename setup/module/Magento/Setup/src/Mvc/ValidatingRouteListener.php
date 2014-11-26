<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Mvc;

use Magento\Setup\Model\Validator;
use Magento\Setup\Mvc\View\Console\ValidationErrorStrategy;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;

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
            $validationMessages .= $this->checkForMissingAndExtraParams($e);
        }

        // Check parameter values
        $validationMessages .= $this->checkParameters($e);
        $this->displayMessage($e, $validationMessages);
        return null;
    }

    /**
     * Checks for missing and extra parameters
     *
     * @param MvcEvent $e
     * @return string
     */
    private function checkForMissingAndExtraParams(MvcEvent $e)
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

            $missing = Validator::checkMissingParameter($expectedParams, $userParams);
            $extra = Validator::checkExtraParameter($expectedParams, $userParams);
            if (!empty($missing)) {
                $validationMessages .= 'Missing parameters:' . PHP_EOL;
                foreach ($missing as $missingParam) {
                    $validationMessages .= $missingParam . PHP_EOL;
                }
            }
            if (!empty($extra)) {
                $validationMessages .= 'Unidentified parameters:' . PHP_EOL;
                foreach ($extra as $extraParam) {
                    $validationMessages .= $extraParam . PHP_EOL;
                }
            }
            if (empty($missing) && empty($extra)) {
                $validationMessages .= 'Please make sure parameters starts with --.' . PHP_EOL .
                    'Note that some parameters require a value while some do not.' . PHP_EOL;
            }

        } else if (!is_null($userAction)) {
            $validationMessages .= "Unknown action name '{$userAction}'." . PHP_EOL;
        }

        // set error to stop propagation
        $e->setError('default_error');
        return $validationMessages;
    }

    /**
     * Check parameter values
     *
     * @param MvcEvent $e
     * @return string
     */
    private function checkParameters(MvcEvent $e)
    {
        $request = $e->getRequest();
        $content = $request->getContent();
        $serviceManager = $e->getApplication()->getServiceManager();
        $routes = $serviceManager->get('Config')['console']['router']['routes'];

        $userAction = $content[0];
        array_shift($content);

        $validationMessages = '';
        /*if (isset($routes[$userAction]['validators'])) {
            $validatorChain = $routes[$userAction]['validators'];
            $pass = true;
            foreach ($validatorChain as $validatorClassName) {
                if (class_exists($validatorClassName)) {
                    $validator = new $validatorClassName();
                    $validatorInterface = 'Magento\\Setup\\Model\\ValidatorInterface';
                    if ($validator instanceof $validatorInterface) {
                        if (!$validator->validate($content)) {
                            $validationMessages .= $validator->getValidationMessages();
                            $pass = false;
                        }
                    }
                }
            }
            if (!$pass) {
                // set error to stop propagation
                $e->setError('Validation_error');
            }
        }*/

        if (isset($routes[$userAction])) {
            $validator = new Validator();
            $userParam = $this->parseUserParams($content);
            if (!$validator->validate($userAction, $userParam)) {
                $validationMessages .= 'Invalid parameter values:' . PHP_EOL . $validator->getValidationMessages();
                // set error to stop propagation
                $e->setError('Validation_error');
            }
        }

        return $validationMessages;
    }

    private function displayMessage(MvcEvent $e, $validationMessages)
    {
        $validationErrorStrategy = new ValidationErrorStrategy();
        $validationErrorStrategy->setErrorMessage($validationMessages);
        $validationErrorStrategy->handleNotFoundError($e);
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
