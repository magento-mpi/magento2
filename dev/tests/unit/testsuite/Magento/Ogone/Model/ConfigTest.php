<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $_model;

    protected function setUp()
    {
        $scopeConfig = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('abcdef1234567890'));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Ogone\Model\Config', [
                'scopeConfig' => $scopeConfig
            ]);
    }

    public function testGetShaInCode()
    {
        $this->assertEquals('abcdef1234567890', $this->_model->getShaInCode());
    }

    public function testGetShaOutCode()
    {
        $this->assertEquals('abcdef1234567890', $this->_model->getShaOutCode());
    }
}
