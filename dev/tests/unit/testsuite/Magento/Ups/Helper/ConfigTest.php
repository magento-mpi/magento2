<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ups\Helper;

/**
 * Config helper Test
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ups config helper
     *
     * @var \Magento\Ups\Helper\Config
     */
    protected $helper;

    public function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManagerHelper->getObject('Magento\Ups\Helper\Config');
    }

    /**
     * @param mixed $result
     * @param null|string $type
     * @param string $code
     * @dataProvider getCodeDataProvider
     */
    public function testGetData($result, $type = null, $code = null)
    {
        $this->assertEquals($result, $this->helper->getCode($type, $code));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getCodeDataProvider()
    {
        return array(
            array(false),
            array(false, 'not-exist-type'),
            array(false, 'not-exist-type', 'not-exist-code'),
            array(false, 'action'),
            array(array('single' => '3', 'all' => '4'), 'action', ''),
            array('3', 'action', 'single')
        );
    }
}
