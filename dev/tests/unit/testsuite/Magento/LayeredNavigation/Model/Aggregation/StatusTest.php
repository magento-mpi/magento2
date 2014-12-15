<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\LayeredNavigation\Model\Aggregation;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\LayeredNavigation\Model\Aggregation\Status */
    private $resolver;

    /** @var ObjectManagerHelper */
    private $objectManagerHelper;

    protected function setUp()
    {
        
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->resolver = $this->objectManagerHelper->getObject('Magento\LayeredNavigation\Model\Aggregation\Status');
    }

    public function testIsEnabled()
    {
        $this->assertTrue($this->resolver->isEnabled());
    }
}
