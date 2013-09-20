<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout;

class MergeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Merge
     */
    private $_model;

    protected function setUp()
    {
        $layoutUtility = new \Magento\Core\Utility\Layout($this);
        $this->_model = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_handles.xml');
    }

    /**
     * Note: test was not relocated to unit tests because of invocation of the static methods
     */
    public function testGetContainers()
    {
        $this->_model->addPageHandles(array('catalog_product_view_type_configurable'));
        $this->_model->load();
        $expected = array(
            'content'                         => __('Main Content Area'),
            'product.info.extrahint'          => __('Product View Extra Hint'),
            'product.info.configurable.extra' => __('Configurable Product Extra Info'),
        );
        $this->assertEquals($expected, $this->_model->getContainers());
    }
}
