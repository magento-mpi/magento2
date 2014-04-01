<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Magento\Review\Test\Block\Product\View\Summary;
use Magento\Review\Test\Block\Product\View;
use Magento\Review\Test\Fixture\Review;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Product reviews functionality
 *
 * @package Magento\Review\Test\TestCase
 */
class ReviewTest extends Functional
{
    /**
     * Adding product review from not logged customer prospective
     *
     * @ZephyrId MAGETWO-12403
     */
    public function testAddReviewByGuest()
    {
        //Preconditions
        $productFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $productFixture->switchData('simple_required');
        $productFixture->persist();
        $reviewFixture = Factory::getFixtureFactory()->getMagentoReviewReview();

        //Pages & Blocks
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $backendReviewPage = Factory::getPageFactory()->getCatalogProductReview();
        $reviewsSummaryBlock = $productPage->getReviewSummaryBlock();
        $reviewsBlock = $productPage->getCustomerReviewBlock();
        $reviewForm = $productPage->getReviewFormBlock();
        $reviewGrid = $backendReviewPage->getGridBlock();
        $reviewBackendForm = $backendReviewPage->getEditForm();

        //Steps & verifying
        $homePage->open();

        $productPage->init($productFixture);
        $productPage->open();
        $this->verifyNoReviewOnPage($reviewsSummaryBlock);
        $reviewsSummaryBlock->getAddReviewLink()->click();
        $this->assertFalse($reviewsBlock->getFirstReview()->isVisible(), 'No reviews below the form required');

        $reviewForm->fill($reviewFixture);
        $reviewForm->submit();
        $submitReviewMessage = 'Your review has been accepted for moderation.';
        $this->assertContains(
            $submitReviewMessage,
            $productPage->getMessagesBlock()->getSuccessMessages(),
            sprintf('Message "%s" is not appear', $submitReviewMessage)
        );
        $this->verifyNoReviewOnPage($productPage->getReviewSummaryBlock());

        Factory::getApp()->magentoBackendLoginUser();
        $backendReviewPage->open();
        $reviewGrid->searchAndOpen(array('title' => $reviewFixture->getTitle()));
        $this->assertEquals('Guest', $reviewBackendForm->getPostedBy(), 'Review is not posted by Guest');
        $this->assertEquals('Pending', $reviewBackendForm->getStatus(), 'Review is not in Pending status');
        $this->assertTrue(
            $reviewBackendForm->verify($reviewFixture),
            'Review data is not corresponds to submitted one'
        );

        $reviewBackendForm->approveReview();
        $this->assertContains(
            'You saved the review.',
            $backendReviewPage->getMessageBlock()->getSuccessMessages(),
            'Review is not saved'
        );

        $this->flushCacheStorageWithAssert();

        $productPage->open();
        $reviewsSummaryBlock = $productPage->getReviewSummaryBlock();
        $this->assertTrue($reviewsSummaryBlock->getAddReviewLink()->isVisible(), 'Add review link is not visible');
        $this->assertTrue($reviewsSummaryBlock->getViewReviewLink()->isVisible(), 'View review link is not visible');
        $this->assertContains(
            '1',
            $reviewsSummaryBlock->getViewReviewLink()->getText(),
            'There is more than 1 approved review'
        );

        $reviewForm = $productPage->getReviewFormBlock();
        $reviewsBlock = $productPage->getCustomerReviewBlock();
        $reviewsSummaryBlock->getViewReviewLink()->click();
        $this->assertContains(
            sprintf('You\'re reviewing:%s', $productFixture->getProductName()),
            $reviewForm->getLegend()->getText()
        );
        $this->verifyReview($reviewsBlock, $reviewFixture);
    }

    /**
     * Check that review is no present on the product page
     *
     * @param Summary $summaryBlock
     */
    protected function verifyNoReviewOnPage(Summary $summaryBlock)
    {
        $noReviewLinkText = 'Be the first to review this product';
        $this->assertEquals(
            $noReviewLinkText,
            trim($summaryBlock->getAddReviewLink()->getText()),
            sprintf('"%s" link is not available', $noReviewLinkText)
        );
    }

    /**
     * Flush cache storage and assert success message
     */
    protected function flushCacheStorageWithAssert()
    {
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushCacheStorage();
        $this->assertTrue($cachePage->getActionsBlock()->isStorageCacheFlushed(), 'Cache is not flushed');
    }

    /**
     * Verify that submitted review is equals data on page
     *
     * @param View $reviewBlock
     * @param Review $fixture
     */
    protected function verifyReview(View $reviewBlock, Review $fixture)
    {
        $reviewItem = $reviewBlock->getFirstReview();
        foreach ($fixture->getData('fields') as $field => $data) {
            $element = $reviewItem->find($reviewBlock->getFieldSelector($field));
            $this->assertEquals(
                strtolower($data['value']),
                strtolower(trim($element->getText())),
                sprintf('Field "%s" is not equals submitted one.', $field)
            );
        }
    }
}
