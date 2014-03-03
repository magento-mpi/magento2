<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\View\Deployment\Version\Generator;

class TimestampTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Timestamp
     */
    private $object;

    protected function setUp()
    {
        $this->object = new Timestamp();
    }

    public function testGenerate()
    {
        $result = $this->object->generate();
        $this->assertNotEmpty($result);
        $this->assertInternalType('string', $result);
        sleep(1);
        $this->assertNotEquals($result, $this->object->generate(), 'Unique value is expected');
    }
}
