<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleNew;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Util\Protocol\CurlTransport;
use \Mtf\System\Config;

/**
 * Test Creation for CreateTargetRuleEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Test Category are created.
 * 2. Products are created (1 product per each category).
 *
 * Steps:
 * 1. Log in as default admin user.
 * 2. Go to Marketing > Related Products Rules
 * 3. Click 'Add Rule' button.
 * 4. Fill in data according to dataSet
 * 5. Save Related Products Rule.
 * 6. Perform all assertions.
 *
 * @group Target_Rules_(MX)
 * @ZephyrId MAGETWO-24686
 */
class CreateTargetRuleEntityTest extends Injectable
{
    /**
     * @var TargetRuleIndex
     */
    protected $targetRuleIndex;

    /**
     * @var TargetRuleNew
     */
    protected $targetRuleNew;

    /**
     * @var TargetRule
     */
    protected $targetRule;

    /**
     * Injection data
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleNew $targetRuleNew
     */
    public function __inject(
        TargetRuleIndex $targetRuleIndex,
        TargetRuleNew $targetRuleNew
    ) {
        $this->targetRuleIndex = $targetRuleIndex;
        $this->targetRuleNew = $targetRuleNew;
    }

    /**
     * Run create TargetRule entity test
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param TargetRule $targetRule
     * @param CustomerSegment|null $customerSegment
     * @return void
     */
    public function testCreateTargetRuleEntity(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        TargetRule $targetRule,
        CustomerSegment $customerSegment = null
    ) {
        // Preconditions:
        $product1->persist();
        $product2->persist();
        if ($customerSegment->hasData()) {
            $customerSegment->persist();
        }
        $replace = $this->getReplaceData($product1, $product2, $customerSegment);

        // Steps
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getGridPageActions()->addNew();
        $this->targetRuleNew->getTargetRuleForm()->fill($targetRule, null, $replace);
        $this->targetRuleNew->getPageActions()->save();

        // Prepare data for tear down
        $this->targetRule = $targetRule;
    }

    /**
     * Get data for replace in variations
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param CustomerSegment $customerSegment
     * @return array
     */
    protected function getReplaceData(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        CustomerSegment $customerSegment = null
    ) {
        $customerSegmentName = ($customerSegment && $customerSegment->hasData()) ? $customerSegment->getName() : '';
        return [
            'rule_information' => [
                'customer_segment_ids' => [
                    '%customer_segment%' => $customerSegmentName,
                ],
            ],
            'products_to_match' => [
                'conditions_serialized' => [
                    '%category_1%' => $product1->getCategoryIds()[0]['id'],
                ],
            ],
            'products_to_display' => [
                'actions_serialized' => [
                    '%category_2%' => $product2->getCategoryIds()[0]['id'],
                ],
            ],
        ];
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $targetRuleId = $this->getTargetRuleId($this->targetRule->getName());
        $url = $_ENV['app_backend_url'] . 'admin/targetrule/delete/id/' . $targetRuleId;
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::POST, $url, '1.0');
        $curl->read();
        $curl->close();
    }

    /**
     * Get TargetRule id by name
     *
     * @param string $name
     * @return int|null
     */
    protected function getTargetRuleId($name)
    {
        $filter = ['name' => $name];
        $url = $_ENV['app_backend_url'] . 'admin/targetrule/index/grid/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="rule_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Encoded filter parameters
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }
}
