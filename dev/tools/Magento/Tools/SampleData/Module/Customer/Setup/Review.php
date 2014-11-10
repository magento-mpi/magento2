<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Customer\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Review
 */
class Review implements SetupInterface
{
    /**
     * @var \Magento\Tools\SampleData\Module\Review\Setup\Review
     */
    protected $setupReview;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccount;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @param \Magento\Tools\SampleData\Module\Review\Setup\Review $setupReview
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Tools\SampleData\Module\Review\Setup\Review $setupReview,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    )
    {
        $this->setupReview = $setupReview;
        $this->customerAccount = $customerAccount;
        $this->reviewFactory = $reviewFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing customers product reviews' . PHP_EOL;

        $fixtureFile = 'Customer/products_reviews.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $row) {
            $productId = $this->setupReview->getProductIdBySku($row['sku']);
            if (!$productId) {
                continue;
            }
            $review = $this->setupReview->prepareReview($row, $productId);
            $review->setCustomerId($this->getCustomerIdByEmail($row['email']));
            $review->save();
            $this->setupReview->setReviewRating($review, $row);
            echo '.';
        }
        echo PHP_EOL;
    }

    /**
     * @param string $customerEmail
     * @return int|null
     */
    protected function getCustomerIdByEmail($customerEmail)
    {
        $customerData = $this->customerAccount->getCustomerByEmail($customerEmail);
        if ($customerData) {
            return $customerData->getId();
        }
        return null;
    }
}