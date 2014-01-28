<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\PageCache\Helper\Data
 */
namespace Magento\PageCache\Helper;

/**
 * Class DataTest
 *
 * @package Magento\PageCache\Controller
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $helper;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $context = $this->getMockBuilder('\Magento\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new \Magento\PageCache\Helper\Data($context);
    }

    public function testGetMaxAgeCache()
    {
        // one year
        $age = 365 * 24 * 60 * 60;
        $data = $this->helper->getMaxAgeCache();
        $this->assertEquals($age, $data);
    }
}
