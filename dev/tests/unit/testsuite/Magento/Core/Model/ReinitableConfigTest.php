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

namespace Magento\Core\Model;

class ReinitableConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testReinit()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $sectionPool = $this->getMock('\Magento\Core\Model\Config\SectionPool', ['clean'], array(), '', false);
        $sectionPool->expects($this->once())
            ->method('clean');
        /** @var \Magento\Core\Model\ReinitableConfig $config */
        $config = $helper->getObject('Magento\Core\Model\ReinitableConfig', ['sectionPool' => $sectionPool]);
        $this->assertInstanceOf('\Magento\App\ReinitableConfigInterface', $config->reinit());
    }
}
