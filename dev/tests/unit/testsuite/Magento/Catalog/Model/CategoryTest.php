<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->objectManager->getObject('Magento\Catalog\Model\Category');
    }

    /**
     * @dataProvider getIdentitiesProvider
     * @param array $expected
     * @param array $origData
     * @param array $data
     * @param bool $isDeleted
     */
    public function testGetIdentities($expected, $origData, $data, $isDeleted = false)
    {
        if (is_array($origData)) {
            foreach ($origData as $key => $value) {
                $this->model->setOrigData($key, $value);
            }
        }
        $this->model->setData($data);
        $this->model->isDeleted($isDeleted);
        $this->assertEquals($expected, $this->model->getIdentities());
    }

    /**
     * @return array
     */
    public function getIdentitiesProvider()
    {
        return array(
            array(
                array('catalog_category_1'),
                array('id' => 1, 'name' => 'value'),
                array('id' => 1, 'name' => 'value')
            ),
            array(
                array('catalog_category_1', 'catalog_category_product_1'),
                null,
                array('id' => 1, 'name' => 'value')
            ),
            array(
                array('catalog_category_1', 'catalog_category_product_1'),
                array('id' => 1, 'name' => ''),
                array('id' => 1, 'name' => 'value')
            ),
            array(
                array('catalog_category_1', 'catalog_category_product_1'),
                array('id' => 1, 'name' => 'value'),
                array('id' => 1, 'name' => 'value'),
                true
            ),
        );
    }
}
