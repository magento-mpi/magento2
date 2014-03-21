<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\DesignEditor\Model\Translate\Inline;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\DesignEditor\Model\Translate\Inline
     */
    protected $translateVde;

    /**
     * @var \Magento\Translate\Inline
     */
    protected $translateInline;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    protected function setUp()
    {
        $this->translateVde = $this->getMock('Magento\DesignEditor\Model\Translate\Inline', [], [], '', false);
        $this->translateInline = $this->getMock('Magento\Translate\Inline', [], [], '', false);
        $this->request = $this->getMock('Magento\App\RequestInterface', [], [], '', false);
    }

    /**
     * @param bool $isVde
     * @param string $instanceName
     *
     * @dataProvider dataProviderGet
     */
    public function testGet($isVde, $instanceName)
    {
        $helper = $this->getMock('Magento\DesignEditor\Helper\Data', [], [], '', false);
        $helper->expects($this->once())
            ->method('isVdeRequest')
            ->will($this->returnValue($isVde));

        $provider = new \Magento\DesignEditor\Model\Translate\Inline\Provider(
            $this->translateVde,
            $this->translateInline,
            $helper,
            $this->request
        );

        $this->assertInstanceOf($instanceName, $provider->get());
    }

    /**
     * @return array
     */
    public function dataProviderGet()
    {
        return [
            [false, 'Magento\Translate\Inline'],
            [true, 'Magento\DesignEditor\Model\Translate\Inline'],
        ];
    }
}
