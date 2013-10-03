<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Model\Product;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    protected $_model;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectHelper->getObject('Magento\Bundle\Model\Product\Type', array(
            'productFactory' => $this->getMock('Magento\Catalog\Model\ProductFactory'),
            'bundleModelSelection' => $this->getMock('Magento\Bundle\Model\SelectionFactory'),
            'bundleFactory' => $this->getMock('Magento\Bundle\Model\Resource\BundleFactory'),
            'bundleCollection' => $this->getMock('Magento\Bundle\Model\Resource\Selection\CollectionFactory'),
            'bundleOption' => $this->getMock('Magento\Bundle\Model\OptionFactory'),
        ));
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
