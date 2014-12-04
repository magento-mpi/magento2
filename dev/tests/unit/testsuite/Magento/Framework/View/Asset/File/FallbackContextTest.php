<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\File;

/**
 * @covers \Magento\Framework\View\Asset\File\FallbackContext
 */
class FallbackContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\View\Asset\File\FallbackContext
     */
    protected $this;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @covers \Magento\Framework\View\Asset\File\FallbackContext::getConfigPath
     * @param string $baseUrl
     * @param string $areaType
     * @param string $themePath
     * @param string $localeCode
     * @param bool $isSecure
     * @param string $expectedResult
     * @dataProvider getConfigPathDataProvider
     */
    public function testGetConfigPath(
        $baseUrl,
        $areaType,
        $themePath,
        $localeCode,
        $isSecure,
        $expectedResult
    ) {
        $this->this = $this->objectManager->getObject(
            'Magento\Framework\View\Asset\File\FallbackContext',
            [
                'baseUrl' => $baseUrl,
                'areaType' => $areaType,
                'themePath' => $themePath,
                'localeCode' => $localeCode,
                'isSecure' => $isSecure
            ]
        );
        $this->assertEquals($expectedResult, $this->this->getConfigPath());
    }

    public function getConfigPathDataProvider()
    {
        return [
            'http' => [
                'baseUrl' => 'http://some-name.com/pub/static/',
                'areaType' => 'frontend',
                'themePath' => 'Magento/blank',
                'localeCode' => 'en_US',
                'isSecure' => false,
                'expectedResult' => 'frontend/Magento/blank/en_US'
            ],
            'https' => [
                'baseUrl' => 'https://some-name.com/pub/static/',
                'areaType' => 'frontend',
                'themePath' => 'Magento/blank',
                'localeCode' => 'en_US',
                'isSecure' => true,
                'expectedResult' => 'frontend/Magento/blank/en_US/secure'
            ]
        ];
    }
}
