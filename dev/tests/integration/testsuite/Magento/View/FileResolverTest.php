<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\FileResolver
     */
    private $object;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->object = $objectManager->create('Magento\View\FileResolver');
    }

    public function testGetPublicFileUrl()
    {
        $pubLibFile = $this->object->getViewFilePublicPath('jquery/jquery.js');
        $actualResult = $this->object->getPublicFileUrl($pubLibFile);
        $this->assertStringEndsWith('/jquery/jquery.js', $actualResult);
    }

    /**
     * @magentoConfigFixture current_store dev/static/sign 1
     */
    public function testGetPublicFileUrlSigned()
    {
        $pubLibFile = $this->object->getViewFilePublicPath('jquery/jquery.js');
        $actualResult = $this->object->getPublicFileUrl($pubLibFile);
        $this->assertStringMatchesFormat('%a/jquery/jquery.js?%d', $actualResult);

        $lastModified = array();
        preg_match('/.*\?(.*)$/i', $actualResult, $lastModified);
        $this->assertArrayHasKey(1, $lastModified);
        $this->assertEquals(10, strlen($lastModified[1]));
        $this->assertLessThanOrEqual(time(), $lastModified[1]);
        $this->assertGreaterThan(1970, date('Y', $lastModified[1]));
    }
} 
