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
 * Integration test for Magento_FullPageCache_Model_Validator
 */
class Magento_FullPageCache_Model_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model under test
     *
     * @var Magento_FullPageCache_Model_Validator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_FullPageCache_Model_Validator');
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
     * @param Magento_Object $object
     * @param boolean $isInvalidated
     *
     * @dataProvider getDataDependenciesDataProvider
     * @covers Magento_FullPageCache_Model_Validator::_getDataChangeDependencies
     * @covers Magento_FullPageCache_Model_Validator::_getDataDeleteDependencies
     *
     * @magentoConfigFixture adminhtml/cache/dependency/change/test Test_Change_Dependency
     * @magentoConfigFixture adminhtml/cache/dependency/delete/test Test_Delete_Dependency
     */
    public function testGetDataDependencies($type, $object, $isInvalidated)
    {
        $cacheType = 'full_page';
        /** @var Magento_Core_Model_Cache_StateInterface $cacheState */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled($cacheType, true);

        /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
        $cacheTypeList = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Cache_TypeListInterface');

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
