<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Request\Aggregation;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Search\Request\Aggregation\Status */
    private $status;

    /** @var ObjectManagerHelper */
    private $objectManagerHelper;

    protected function setUp()
    {
        
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->status = $this->objectManagerHelper->getObject('Magento\Framework\Search\Request\Aggregation\Status');
    }

    public function testIsEnabled()
    {
        $this->assertFalse($this->status->isEnabled());
    }
}
