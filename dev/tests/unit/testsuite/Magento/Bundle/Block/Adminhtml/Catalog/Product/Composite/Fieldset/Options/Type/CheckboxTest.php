<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type;

use Magento\TestFramework\Helper\ObjectManager;

class CheckboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\Checkbox
     */
    protected $block;

    public function setUp()
    {
        $this->block = (new ObjectManager($this))
            ->getObject('Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\Checkbox');
    }

    public function testSetValidationContainer()
    {
        $elementId = 'element-id';
        $containerId = 'container-id';

        $result = $this->block->setValidationContainer($elementId, $containerId);

        $this->assertContains($elementId, $result);
        $this->assertContains($containerId, $result);
    }
}
