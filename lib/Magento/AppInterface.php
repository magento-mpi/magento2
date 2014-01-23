<?php
/**
 * Application. Performs user requested actions.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

interface AppInterface
{
    /**
     * @return \Magento\App\ResponseInterface
     */
    public function execute();
} 