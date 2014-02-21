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
        $this->helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Ups\Helper\Config');
    }

    /**
     * @param mixed $result
     * @param null|string $type
     * @param string $code
     * @dataProvider getCodeDataProvider
     */
    public function testGetData($result, $type = null, $code = null) {

        $this->assertEquals($result, $this->helper->getCode($type, $code));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getCodeDataProvider()
    {
        return [
            [false],
            [false, 'not-exist-type'],
            [false, 'not-exist-type', 'not-exist-code'],
            [false, 'action'],
            [['single' => '3', 'all' => '4'], 'action', ''],
            ['3', 'action', 'single'],
        ];
    }
}
