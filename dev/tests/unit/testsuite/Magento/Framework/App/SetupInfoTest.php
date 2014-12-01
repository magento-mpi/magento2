<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;


class SetupInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $server
     * @param string $expected
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($server, $expected)
    {
        $info = new SetupInfo($server);
        $this->assertEquals($expected, $info->getUrl());
    }

    /**
     * @return array
     */
    public function getUrlDataProvider()
    {
        return [
            [
                [],
                '/setup/'
            ],
            [
                [SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => 'install'],
                '/install/',
            ],
            [
                [SetupInfo::PARAM_NOT_INSTALLED_URL => 'http://example.com/'],
                'http://example.com/',
            ],
        ];
    }

    /**
     * @param array $server
     * @param string $projectRoot
     * @param string $expected
     * @dataProvider getDirDataProvider
     */
    public function testGetDir($server, $projectRoot, $expected)
    {
        $info = new SetupInfo($server);
        $this->assertEquals($expected, $info->getDir($projectRoot));
    }

    /**
     * @return array
     */
    public function getDirDataProvider()
    {
        return [
            [
                [],
                '/test/root',
                '/test/root/setup',
            ],
            [
                [],
                '/test/root/',
                '/test/root/setup',
            ],
            [
                [SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => '/install/'],
                '/test/',
                '/test/install',
            ],
        ];
    }

    /**
     * @param array $server
     * @param string $projectRoot
     * @param bool $expected
     * @dataProvider isAvailableDataProvider
     */
    public function testIsAvailable($server, $projectRoot, $expected)
    {
        $info = new SetupInfo($server);
        $this->assertEquals($expected, $info->isAvailable($projectRoot));
    }

    /**
     * @return array
     */
    public function isAvailableDataProvider()
    {
        return [
            'no doc root defined' => [
                [],
                'anything',
                false
            ],
            'root = doc root, but no "setup" sub-directory' => [
                ['DOCUMENT_ROOT' => __DIR__], // it will look for "setup/" sub-directory, but won't find anything
                __DIR__,
                false
            ],
            'root = doc root, nonexistent sub-directory' => [
                ['DOCUMENT_ROOT' => __DIR__, SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => 'nonexistent'],
                __DIR__,
                false
            ],
            'root = doc root, existent sub-directory' => [
                ['DOCUMENT_ROOT' => __DIR__, SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => '_files'],
                __DIR__,
                true
            ],
            'root within doc root, existent sub-directory' => [
                ['DOCUMENT_ROOT' => dirname(__DIR__), SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => '_files'],
                __DIR__,
                true
            ],
            'root outside of doc root, existent sub-directory' => [
                ['DOCUMENT_ROOT' => __DIR__, SetupInfo::PARAM_NOT_INSTALLED_URL_PATH => '_files'],
                dirname(__DIR__),
                false
            ],
        ];
    }
}
