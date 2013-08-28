<?php
/**
 * Test class for Enterprise_PageCache_Model_DesignPackage_Rules
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_DesignPackage_RulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designChangeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designMock;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @var array
     */
    protected $_designChange;

    /**
     * @var string
     */
    protected $_currentDate;

    /**
     * @var int
     */
    protected $_storeId = 1;

    protected function setUp()
    {
        $this->_fpcCacheMock = $this->getMock('Enterprise_PageCache_Model_Cache', array(), array(), '', false);
        $this->_designChangeMock = $this->getMock('Magento_Core_Model_Design', array(), array(), '', false);
        $this->_designMock = $this->getMock('Magento_Core_Model_View_DesignInterface');

        $this->_currentDate = date('Y-m-d');

        $this->_cacheId = Enterprise_PageCache_Model_DesignPackage_Rules::DESIGN_CHANGE_CACHE_SUFFIX
            . '_'. md5($this->_storeId . $this->_currentDate);

        $this->_designChange = array('design' => 'design_change', 'store_id' => $this->_storeId);
    }

    public function testGetPackageNameWithCachedIdAndWithoutDesignException()
    {
        $cache = array('design' => 'design_change');

        $valueMap = array(
            array(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY, false),
            array($this->_cacheId, serialize($cache)),
        );

        $this->_designChangeMock->expects($this->never())->method('getResource');

        $this->_fpcCacheMock->expects($this->exactly(2))
            ->method('load')
            ->will($this->returnValueMap($valueMap));

        $model = new Enterprise_PageCache_Model_DesignPackage_Rules(
            $this->_designChangeMock,
            $this->_designMock,
            $this->_fpcCacheMock
        );

        $this->assertEquals('design_change', $model->getPackageName($this->_storeId));
    }

    public function testGetPackageNameWithoutCachedIdAndWithoutDesignException()
    {
        $resourceMock = $this->getMock('Magento_Core_Model_Resource_Design', array(), array(), '', false);
        $resourceMock->expects($this->once())
            ->method('loadChange')
            ->with($this->_storeId, $this->_currentDate)
            ->will($this->returnValue($this->_designChange));

        $this->_designChangeMock
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resourceMock));

        $valueMap = array(
            array(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY, false),
            array($this->_cacheId, false),
        );

        $this->_fpcCacheMock->expects($this->exactly(2))
            ->method('load')
            ->will($this->returnValueMap($valueMap));

        $this->_fpcCacheMock->expects($this->once())
            ->method('save')
            ->with(
                serialize($this->_designChange),
                $this->_cacheId,
                array(Enterprise_PageCache_Model_Processor::CACHE_TAG),
               86400
            );


        $model = new Enterprise_PageCache_Model_DesignPackage_Rules(
            $this->_designChangeMock,
            $this->_designMock,
            $this->_fpcCacheMock
        );

        $this->assertEquals('design_change', $model->getPackageName($this->_storeId));

    }

    public function testGetPackageNameWithCachedIdAndWithDesignException()
    {
        $cache = array('design' => 'design_change');
        $designException = array(
            'regexp' => 'some_reg_exp',
            'value' => 'some-value',
        );

        $valueMap = array(
            array(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY, $designException),
            array($this->_cacheId, serialize($cache)),
        );

        $this->_designChangeMock->expects($this->never())->method('getResource');

        $this->_fpcCacheMock->expects($this->exactly(2))
            ->method('load')
            ->will($this->returnValueMap($valueMap));


        $model = $this->getMock('Enterprise_PageCache_Model_DesignPackage_Rules',
            array('_getPackageByUserAgent'),
            array(
                $this->_designChangeMock,
                $this->_designMock,
                $this->_fpcCacheMock,
            )
        );

        $model->expects($this->once())->method('_getPackageByUserAgent')
            ->with($designException)->will($this->returnValue('design_name'));

        $this->assertEquals('design_name', $model->getPackageName($this->_storeId));
    }
}
