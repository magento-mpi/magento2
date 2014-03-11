<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Controller;

/**
 * @magentoDataFixture Magento/Catalog/_files/products.php
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoAppArea frontend
 */
class ProductTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Customer\Service\V1\Data\Customer
     */
    protected $customer;

    protected function setUp()
    {
        parent::setUp();
        $customerSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Customer\Model\Session');
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountService');
        $this->customer = $service->authenticate('customer@example.com', 'password');
        $customerSession->setCustomerDataAsLoggedIn($this->customer);
    }

    /**
     * @dataProvider listActionDesignDataProvider
     */
    public function testListActionDesign($productId, $expectedDesign)
    {
        $this->getRequest()->setParam('id', $productId);
        $this->dispatch('review/product/list');
        $result = $this->getResponse()->getBody();
        $this->assertContains("static/frontend/{$expectedDesign}/en_US/Magento_Theme/favicon.ico", $result);
    }

    /**
     * @return array
     */
    public function listActionDesignDataProvider()
    {
        return array(
            'custom product design' => array(2, 'magento_blank'),
        );
    }

    public function testPostAction()
    {
        /** @var $formKey \Magento\Data\Form\FormKey */
        $formKey = $this->_objectManager->get('Magento\Data\Form\FormKey');
        $ratings = [1 => 1, 2 => 2, 3 => 3];
        $this->getRequest()->setPost(
            [
                'id' => 2,
                'form_key' => $formKey->getFormKey(),
                'title' => 'Review Title',
                'nickname' => 'Nickname',
                'detail' => 'Review Detail',
                'ratings' => $ratings
            ]
        );
        $this->dispatch('review/product/post');
        /** @var \Magento\Review\Model\Review $review */
        $review = $this->_objectManager->create('Magento\Review\Model\Resource\Review\Collection')
            ->addCustomerFilter($this->customer->getId())
            ->getFirstItem();
        $this->assertNotEmpty($review);
        $votes = $this->_objectManager->create('Magento\Rating\Model\Resource\Rating\Option\Vote\Collection')
            ->setReviewFilter($review->getId());
        $this->assertCount(count($ratings), $votes);
        foreach ($votes as $vote) {
            $this->assertEquals($this->customer->getId(), $vote->getCustomerId());
        }
    }
}
