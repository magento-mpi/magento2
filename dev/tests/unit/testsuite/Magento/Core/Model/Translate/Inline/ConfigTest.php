<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Translate\Inline;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testIsActive()
    {
        $store = 'some store';
        $result = 'result';
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $coreStoreConfig
            ->expects($this->once())
            ->method('getConfigFlag')
            ->with($this->equalTo('dev/translate_inline/active'), $this->equalTo($store))
            ->will($this->returnValue($result));
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $config = $objectManager->getObject(
            '\Magento\Core\Model\Translate\Inline\Config',
            array(
                'coreStoreConfig' => $coreStoreConfig
            )
        );
        $this->assertEquals($result, $config->isActive($store));
    }
}
