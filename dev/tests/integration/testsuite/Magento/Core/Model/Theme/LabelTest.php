<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Theme_LabelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme\Label
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\Core\Model\Theme\Label');
    }

    /**
     * @covers \Magento\Core\Model\Theme\Label::getLabelsCollection
     */
    public function testGetLabelsCollection()
    {
        /** @var $expectedCollection \Magento\Core\Model\Resource\Theme\Collection */
        $expectedCollection = Mage::getModel('Magento\Core\Model\Resource\Theme\Collection');
        $expectedCollection->addAreaFilter(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            ->filterVisibleThemes();

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $this->_model->getLabelsCollection();
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $this->_model->getLabelsCollection('-- Please Select --');
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
