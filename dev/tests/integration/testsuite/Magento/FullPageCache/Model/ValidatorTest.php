<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Integration test for \Magento\FullPageCache\Model\Validator
 */
class Magento_FullPageCache_Model_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model under test
     *
     * @var \Magento\FullPageCache\Model\Validator
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento\FullPageCache\Model\Validator');
    }

    /**
     * Data provider for testGetDataDependencies
     *
     * @return array
     */
    public function getDataDependenciesDataProvider()
    {
        // test dependency classes are added to config in testGetDataDependencies
        $changeDependency = $this->getMock('stdClass', array(), array(), 'Test_Change_Dependency');
        $deleteDependency = $this->getMock('stdClass', array(), array(), 'Test_Delete_Dependency');

        return array(
            'change_class_for_caching' => array(
                '$type'          => 'change',
                '$object'        => $changeDependency,
                '$isInvalidated' => true,
            ),
            'change_class_not_for_caching' => array(
                '$type'          => 'change',
                '$object'        => new stdClass(),
                '$isInvalidated' => false,
            ),
            'delete_class_for_caching' => array(
                '$type'          => 'delete',
                '$object'        => $deleteDependency,
                '$isInvalidated' => true,
            ),
            'delete_class_not_for_caching' => array(
                '$type'          => 'delete',
                '$object'        => new stdClass(),
                '$isInvalidated' => false,
            ),
        );
    }

    /**
     * Test for both _getDataChangeDependencies and _getDataDeleteDependencies
     *
     * @param string $type
     * @param \Magento\Object $object
     * @param boolean $isInvalidated
     *
     * @dataProvider getDataDependenciesDataProvider
     * @covers \Magento\FullPageCache\Model\Validator::_getDataChangeDependencies
     * @covers \Magento\FullPageCache\Model\Validator::_getDataDeleteDependencies
     *
     * @magentoConfigFixture adminhtml/cache/dependency/change/test Test_Change_Dependency
     * @magentoConfigFixture adminhtml/cache/dependency/delete/test Test_Delete_Dependency
     */
    public function testGetDataDependencies($type, $object, $isInvalidated)
    {
        $cacheType = 'full_page';
        /** @var \Magento\Core\Model\Cache\StateInterface $cacheState */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled($cacheType, true);

        /** @var \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList */
        $cacheTypeList = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Cache\TypeListInterface');

        // manual unset cache type
        $cacheTypeList->cleanType($cacheType);

        // invoke get data dependencies method
        switch ($type) {
            case 'change':
                $this->_model->checkDataChange($object); // invokes _getDataChangeDependencies
                break;

            case 'delete':
                $this->_model->checkDataDelete($object); // invokes _getDataDeleteDependencies
                break;

            default:
                break;
        }

        // assert cache invalidation status
        $invalidatedTypes = $cacheTypeList->getInvalidated();
        if ($isInvalidated) {
            $this->assertArrayHasKey($cacheType, $invalidatedTypes);
        } else {
            $this->assertArrayNotHasKey($cacheType, $invalidatedTypes);
        }
    }
}
