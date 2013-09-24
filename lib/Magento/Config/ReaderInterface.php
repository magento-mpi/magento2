<?php
/**
 * Reader responsible for retrieving provided scope of configuration from storage
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Config_ReaderInterface
{
    /**
     * Read configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null);
}
