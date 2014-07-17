<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @magentoAppArea adminhtml
 */
class TaxTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @dataProvider ajaxActionDataProvider
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
        $class = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Tax\Model\ClassModel')
            ->load($classId, 'class_id');
        $this->assertEquals($expectedData['class_name'], $class->getClassName());
    }

    /**
     * @dataProvider ajaxActionDataProvider
     * @magentoDbIsolation enabled
     *
     * @param array $taxClassData
     */
    public function testAjaxDeleteAction($taxClassData)
    {
        $taxClassService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Tax\Service\V1\TaxClassServiceInterface'
        );

        $taxClassBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Tax\Service\V1\Data\TaxClassBuilder'
        );

        $taxClass = $taxClassBuilder->setClassName($taxClassData['class_name'])
            ->setClassType($taxClassData['class_type'])
            ->create();

        $taxClassId = $taxClassService->createTaxClass($taxClass);

        /** @var $rate \Magento\Tax\Model\ClassModel */
        $class = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Tax\Model\ClassModel')
            ->load($taxClassId, 'class_id');
        $this->assertEquals($taxClassData['class_name'], $class->getClassName());
        $this->assertEquals($taxClassData['class_type'], $class->getClassType());

        $postData = [ 'class_id' => $taxClassId ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/tax/tax/ajaxDelete');

        $isFound = true;
        try {
            $taxClassId = $taxClassService->getTaxClass($taxClassId);
        } catch (NoSuchEntityException $e) {
            $isFound = false;
        }
        $this->assertFalse($isFound, "Tax Class was found when it should have been deleted.");
    }

    /**
     * @return array
     */
    public function ajaxActionDataProvider()
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
