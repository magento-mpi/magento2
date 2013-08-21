<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_FormTest
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get form instance
     *
     * @param array $args
     * @return Magento_Data_Form
     */
    protected function _getFormInstance($args = array())
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form */
        $block = $layout->createBlock(
            'Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', 'block', array('data' => $args)
        );
        $block->setTemplate(null);
        $block->toHtml();
        return $block->getForm();
    }

    /**
     * Check _formPostInit set expected fields values
     *
     * @covers Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form::_formPostInit
     *
     * @dataProvider formPostInitDataProvider
     *
     * @param array $cmsPageData
     * @param string $action
     * @param string $idPath
     * @param string $requestPath
     * @param string $targetPath
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testFormPostInit($cmsPageData, $action, $idPath, $requestPath, $targetPath)
    {
        $args = array();
        if ($cmsPageData) {
            $args['cms_page'] = new Magento_Object($cmsPageData);
        }
        $form = $this->_getFormInstance($args);
        $this->assertContains($action, $form->getAction());

        $this->assertEquals($idPath, $form->getElement('id_path')->getValue());
        $this->assertEquals($requestPath, $form->getElement('request_path')->getValue());
        $this->assertEquals($targetPath, $form->getElement('target_path')->getValue());

        $this->assertTrue($form->getElement('id_path')->getData('disabled'));
        $this->assertTrue($form->getElement('target_path')->getData('disabled'));
    }

    /**
     * Test entity stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testGetEntityStores()
    {
        $args = array(
            'cms_page' => $this->_getCmsPageWithStoresMock(array(1))
        );
        $form = $this->_getFormInstance($args);

        $expectedStores = array(
            array(
                'label' => 'Main Website',
                'value' => array()
            ),
            array(
                'label' => '    Main Website Store',
                'value' => array(
                    array(
                        'label' => '    Default Store View',
                        'value' => 1
                    )
                )
            )
        );
        $this->assertEquals($expectedStores, $form->getElement('store_id')->getValues());
    }

    /**
     * Check exception is thrown when product does not associated with stores
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     *
     * @expectedException Magento_Core_Model_Store_Exception
     * @expectedExceptionMessage Chosen cms page does not associated with any website.
     */
    public function testGetEntityStoresProductStoresException()
    {
        $args = array(
            'cms_page' => $this->_getCmsPageWithStoresMock(array())
        );
        $this->_getFormInstance($args);
    }

    /**
     * Data provider for testing formPostInit
     * 1) Cms page is selected
     *
     * @static
     * @return array
     */
    public static function formPostInitDataProvider()
    {
        return array(
            array(
                array('id' => 3, 'identifier' => 'cms-page'),
                'cms_page/3', 'cms_page/3', 'cms-page', 'cms/page/view/page_id/3'
            )
        );
    }

    /**
     * Get CMS page model mock
     *
     * @param $stores
     * @return PHPUnit_Framework_MockObject_MockObject|Magento_Cms_Model_Page
     */
    protected function _getCmsPageWithStoresMock($stores)
    {
        $resourceMock = $this->getMockBuilder('Magento_Cms_Model_Resource_Page')
            ->setMethods(array('lookupStoreIds'))
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->expects($this->any())
            ->method('lookupStoreIds')
            ->will($this->returnValue($stores));

        $cmsPageMock = $this->getMockBuilder('Magento_Cms_Model_Page')
            ->setMethods(array('getResource', 'getId'))
            ->disableOriginalConstructor()
            ->getMock();
        $cmsPageMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $cmsPageMock->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resourceMock));

        return $cmsPageMock;
    }
}
