<?php
/**
 * Magento application action
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface ActionInterface
{
    /**
     * Dispatch controller action
     *
     * @abstract
     * @param string $action action name
     * @return void
     */
    public function dispatch($action);
}
