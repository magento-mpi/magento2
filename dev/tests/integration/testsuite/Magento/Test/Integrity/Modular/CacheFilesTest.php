<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class CacheFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @dataProvider cacheConfigDataProvider
     */
    public function testCacheConfig($area)
    {
        $validationStateMock = $this->getMock('Magento\Framework\Config\ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Cache\Config\Reader $reader */
        $reader = $objectManager->create(
            'Magento\Cache\Config\Reader',
            array('validationState' => $validationStateMock)
        );
        try {
            $reader->read($area);
        } catch (\Magento\Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function cacheConfigDataProvider()
    {
        return array('global' => array('global'), 'adminhtml' => array('adminhtml'), 'frontend' => array('frontend'));
    }
}
