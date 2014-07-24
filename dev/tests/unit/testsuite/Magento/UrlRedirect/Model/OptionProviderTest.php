<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRedirect\Model;

class OptionProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        $model = new OptionProvider();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array('' => 'No', 'R' => 'Temporary (302)', 'RP' => 'Permanent (301)');
        $this->assertEquals($expectedOptions, $options);
    }
}
