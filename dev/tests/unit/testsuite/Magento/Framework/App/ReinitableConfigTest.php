<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class ReinitableConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testReinit()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $scopePool = $this->getMock('\Magento\Framework\App\Config\ScopePool', array('clean'), array(), '', false);
        $scopePool->expects($this->once())->method('clean');
        /** @var \Magento\Core\Model\ReinitableConfig $config */
        $config = $helper->getObject('Magento\Framework\App\ReinitableConfig', array('scopePool' => $scopePool));
        $this->assertInstanceOf('\Magento\Framework\App\Config\ReinitableConfigInterface', $config->reinit());
    }
}
