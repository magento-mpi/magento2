<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Mvc\View\Http;

use Zend\EventManager\EventManagerInterface as Events;
use Zend\Mvc\View\Http\InjectTemplateListener as ZendInjectTemplateListener;
use Zend\Mvc\MvcEvent;

class InjectTemplateListener extends ZendInjectTemplateListener
{
    /**
     * Determine the top-level namespace of the controller
     *
     * @param  string $controller
     * @return string
     */
    protected function deriveModuleNamespace($controller)
    {
        if (!strstr($controller, '\\')) {
            return '';
        }

        // Retrieve the first two elemenents representing the vendor and module name.
        $nsArray = explode('\\', $controller);
        $subNsArray = array_slice($nsArray, 0, 2);
        return implode('/', $subNsArray);
    }

    /**
     * @param $namespace
     * @return string
     */
    protected function deriveControllerSubNamespace($namespace)
    {
        if (!strstr($namespace, '\\')) {
            return '';
        }
        $nsArray = explode('\\', $namespace);

        // Remove the first three elements representing the vendor, module name and controller directory.
        $subNsArray = array_slice($nsArray, 3);
        if (empty($subNsArray)) {
            return '';
        }
        return implode('/', $subNsArray);
    }

    /**
     * Inject a template into the view model, if none present
     *
     * Template is derived from the controller found in the route match, and,
     * optionally, the action, if present.
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function injectTemplate(MvcEvent $e)
    {
        $e->getRouteMatch()->setParam('action', null);
        parent::injectTemplate($e);
    }
}
