<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class ReinitableConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testReinit()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $scopePool = $this->getMock('\Magento\App\Config\ScopePool', ['clean'], array(), '', false);
        $scopePool->expects($this->once())
            ->method('clean');
        /** @var \Magento\Core\Model\ReinitableConfig $config */
        $config = $helper->getObject('Magento\App\ReinitableConfig', ['scopePool' => $scopePool]);
        $this->assertInstanceOf('\Magento\App\ReinitableConfigInterface', $config->reinit());
    }
}
