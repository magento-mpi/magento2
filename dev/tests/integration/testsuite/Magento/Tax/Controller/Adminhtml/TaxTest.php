<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class TaxTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @dataProvider ajaxSaveActionDataProvider
     * @magentoDbIsolation enabled
     *
     * @param array $postData
     * @param array $expectedData
     */
    public function testAjaxSaveAction($postData, $expectedData)
    {
        $this->getRequest()->setPost($postData);

        $this->dispatch('backend/tax/tax/ajaxSave');

        $jsonBody = $this->getResponse()->getBody();
        $result = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Helper\Data'
        )->jsonDecode(
            $jsonBody
        );

        $this->assertArrayHasKey('class_id', $result);

        $classId = $result['class_id'];
        /** @var $rate \Magento\Tax\Model\ClassModel */
        $class = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tax\Model\ClassModel'
        )->load(
            $classId,
            'class_id'
        );
        $this->assertEquals($expectedData['class_name'], $class->getClassName());
    }

    /**
     * @return array
     */
    public function ajaxSaveActionDataProvider()
    {
        return array(
            array(
                array('class_type' => 'CUSTOMER', 'class_name' => 'Class Name'),
                array('class_name' => 'Class Name')
            ),
            array(
                array('class_type' => 'PRODUCT', 'class_name' => '11111<22222'),
                array('class_name' => '11111&lt;22222')
            ),
            array(
                array('class_type' => 'CUSTOMER', 'class_name' => '   12<>sa&df    '),
                array('class_name' => '12&lt;&gt;sa&amp;df')
            )
        );
    }
}
