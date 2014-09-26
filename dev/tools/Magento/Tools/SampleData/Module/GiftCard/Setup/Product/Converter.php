<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\GiftCard\Setup\Product;

/**
 * Class Converter
 */
class Converter
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface
     */
    protected $categoryReadService;

    /**
     * @param \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService
     */
    public function __construct(
        \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService
    ) {
        $this->categoryReadService = $categoryReadService;
    }

    /**
     * Convert CSV row array into array for GiftCard
     *
     * @param array $row
     * @return array
     */
    public function convertRow(array $row)
    {
        $weight = 1;
        $data = [];
        foreach ($row as $field => $value) {
            switch ($field) {
                case 'category':
                    $data['category_ids'] = $this->getCategoryIds($this->getArrayValue($value));
                    break;
                case 'price':
                    $prices = $this->getArrayValue($value);
                    $i = -1;
                    foreach ($prices as $price) {
                        if (is_numeric($price)) {
                            $data['giftcard_amounts'][++$i]['website_id'] = 0;
                            $data['giftcard_amounts'][$i]['price'] = $price;
                            $data['giftcard_amounts'][$i]['delete'] = null;
                        } else if ($price == 'Custom') {
                            $data['allow_open_amount'] = \Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_ENABLED;
                            $data['open_amount_min'] = min($prices);
                            $data['open_amount_max'] = null;
                        }
                    }
                    break;
                case 'format':
                    switch ($value) {
                        case 'Virtual':
                            $data['giftcard_type'] = \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL;
                            break;
                        case 'Physical':
                            $data['giftcard_type'] = \Magento\GiftCard\Model\Giftcard::TYPE_PHYSICAL;
                            $data['weight'] = $weight;
                            break;
                        case 'Combined':
                            $data['giftcard_type'] = \Magento\GiftCard\Model\Giftcard::TYPE_COMBINED;
                            $data['weight'] = $weight;
                            break;
                    }
                    break;
                default:
                    $data[$field] = $value;
                    break;
            }
        }
        return $data;
    }

    /**
     * Convert strings with EOL into array
     *
     * @param $value
     * @return array
     */
    protected function getArrayValue($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (false !== strpos($value, "\n")) {
            $value = array_filter(explode("\n", $value));
        }
        return !is_array($value) ? [$value] : $value;
    }

    /**
     * Get ids for given category names
     *
     * @param array $categories
     * @return array
     */
    protected function getCategoryIds(array $categories)
    {
        $ids = [];
        $tree = $this->categoryReadService->tree();
        foreach ($categories as $name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $tree = $child;
                    $ids[] = $child->getId();
                    break;
                }
            }
        }
        return $ids;
    }
}
