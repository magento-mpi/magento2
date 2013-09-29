<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class TestReaderClass
{
    public function read($scope = 'primary')
    {
        return $scope;
    }
}