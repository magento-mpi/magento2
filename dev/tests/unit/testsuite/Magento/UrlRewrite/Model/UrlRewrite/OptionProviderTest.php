<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\UrlRewrite\Model\UrlRewrite\OptionProvider.
 */
namespace Magento\UrlRewrite\Model\UrlRewrite;

class OptionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\UrlRewrite\Model\UrlRewrite\OptionProvider::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new \Magento\UrlRewrite\Model\UrlRewrite\OptionProvider();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array('' => 'No', 'R' => 'Temporary (302)', 'RP' => 'Permanent (301)');
        $this->assertEquals($expectedOptions, $options);
    }
}
