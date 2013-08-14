<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_MergeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Merge
     */
    private $_model;

    protected function setUp()
    {
        $layoutUtility = new Magento_Core_Utility_Layout($this);
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
            'content'                         => 'Main Content Area',
            'product.info.extrahint'          => 'Product View Extra Hint',
            'product.info.configurable.extra' => 'Configurable Product Extra Info',
        );
        $this->assertSame($expected, $this->_model->getContainers());
    }
}
