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
     * @var Category
     */
    protected $model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $flatState;

    protected function setUp()
    {
        $this->filter = $this->getMock('Magento\Framework\Filter\FilterManager', ['translitUrl'], [], '', false);
        $this->flatState = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State',
            ['isAvailable'],
            [],
            '',
            false
        );
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->objectManager->getObject(
            'Magento\Catalog\Model\Category',
            [
                'filter' => $this->filter,
                'flatState' => $this->flatState,
            ]
        );
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
            $this->model->setData($origData);
            $this->model->setOrigData();
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
                array('catalog_category_1', 'catalog_category_product_1'),
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

    public function testFormatUrlKey()
    {
        $strIn = 'Some string';
        $resultString = 'some';

        $this->filter->expects(
            $this->once()
        )->method(
            'translitUrl'
        )->with(
            $strIn
        )->will(
            $this->returnValue($resultString)
        );

        $this->assertEquals($resultString, $this->model->formatUrlKey($strIn));
    }

    public function testGetUseFlatResourceFalse()
    {
        $this->assertEquals(false, $this->model->getUseFlatResource());
    }

    public function testGetUseFlatResourceTrue()
    {
        $this->flatState->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $this->model = $this->objectManager->getObject(
            'Magento\Catalog\Model\Category',
            [
                'filter' => $this->filter,
                'flatState' => $this->flatState,
            ]
        );

        $this->assertEquals(true, $this->model->getUseFlatResource());
    }
}
