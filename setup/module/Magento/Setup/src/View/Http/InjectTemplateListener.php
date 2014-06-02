<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\View\Http;

use Zend\EventManager\EventManagerInterface as Events;
use Zend\Mvc\View\Http\InjectTemplateListener as ZendInjectTemplateListener;

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
}
