<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData\Helper;

class Fixture
{
    /**
     * @todo replace to real path to fixtures
     *
     * @param string $subPath
     * @return string
     */
    public function getPath($subPath)
    {
        return realpath(__DIR__ . '/../fixtures/' . ltrim($subPath, '/'));
    }
}