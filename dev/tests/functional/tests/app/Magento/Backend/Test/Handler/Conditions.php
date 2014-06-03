<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Handler;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Conditions
 * Curl class for fixture with conditions
 *
 * Format value of conditions.
 * Add slash to symbols: "{", "}", "[", "]", ":".
 * 1. Single condition:
 * [Type|Param|Param|...|Param]
 * 2. List conditions:
 * [Type|Param|Param|...|Param]
 * [Type|Param|Param|...|Param]
 * [Type|Param|Param|...|Param]
 * 3. Combination condition with single condition
 * {Type|Param|Param|...|Param:[Type|Param|Param|...|Param]}
 * 4. Combination condition with list conditions
 * {Type|Param|Param|...|Param:[[Type|Param|...|Param][Type|Param|...|Param]...[Type|Param|...|Param]]}
 *
 * Example value:
 * {Products subselection|total amount|greater than|135|ANY:[[Price in cart|is|100][Quantity in cart|is|100]]}
 * {Conditions combination:[
 *     [Subtotal|is|100]
 *     {Product attribute combination|NOT FOUND|ANY:[[Attribute Set|is|Default][Attribute Set|is|Default]]}
 * ]}
 */
class Conditions extends AbstractCurl {
    /**
     * Map of type parameter
     *
     * @var array
     */
    private $mapTypeParams = [
        'Attribute Set' => [
            'type' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
            'attribute' => 'attribute_set_id'
        ],
        'Category' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
            'attribute' => 'category_ids'
        ],
        'Conditions combination' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
            'aggregator' => 'all',
            'value' => '1'
        ],
        'Price (percentage)' => [
            'type' => 'Magento\TargetRule\Model\Actions\Condition\Product\Special\Price',
        ],
        'Subtotal' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
            'attribute' => 'base_subtotal'
        ],
        'Total Weight' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
            'attribute' => 'weight'
        ],
        'Shipping Method' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
            'attribute' => 'shipping_method'
        ],
        'Shipping Postcode' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
            'attribute' => 'postcode'
        ],
        'Shipping State/Province' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
            'attribute' => 'region_id'
        ],
        'Shipping Country' => [
            'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
            'attribute' => 'country_id'
        ],
    ];
    /**
     * Map of rule parameters
     *
     * @var array
     */
    private $mapRuleParams = [
        'operator' => [
            'is' => '==',
            'is not' => '!=',
            'equal to' => '==',
        ],
        'value_type' => [
            'same_as' => 'the Same as Matched Product Categories',
        ],
        'value' => [
            'California' => '12',
            'United States' => 'US',
            '[flatrate] Fixed' => 'flatrate_flatrate',
        ],
        'aggregator' => [
            'ALL' => 'all',
        ],
    ];

    /**
     * Persist Fixture
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed
     */
    public function persist(FixtureInterface $fixture = null) {

    }

    /**
     * Prepare conditions to array for send by post request
     *
     * @param string $conditions
     * @return array
     */
    protected function prepareCondition($conditions) {
        $conditions = $this->decodeValue($conditions);
        $defaultCondition = [
            1 => [
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                'aggregator' => 'all',
                'value' => '1'
            ]
        ];
        return $defaultCondition + $this->convertMultipleCondition($conditions);
    }

    /**
     * Convert condition combination
     *
     * @param string $combination
     * @param array|string $conditions
     * @param int $nesting
     * @return array
     */
    private function convertConditionsCombination($combination, $conditions, $nesting) {
        $combination = [$nesting => $this->convertSingleCondition($combination)];
        $conditions = $this->convertMultipleCondition($conditions, $nesting);
        return $combination + $conditions;
    }

    /**
     * Convert multiple condition
     *
     * @param array $conditions
     * @param int $nesting
     * @param int $count
     * @return array
     */
    protected function convertMultipleCondition(array $conditions, $nesting = 1, $count = 1)
    {
        $result = [];
        foreach ($conditions as $key => $condition) {
            if (!is_numeric($key)) {
                $nesting = $nesting . '--' . $count;
                $result += $this->convertConditionsCombination($key, $condition, $nesting);
            } elseif (is_string($condition)) {
                $result[$nesting . '--' . $count] = $this->convertSingleCondition($condition);
            } else {
                $result += $this->convertMultipleCondition($condition, $nesting, $count);
            }
            $count++;
        }
        return $result;
    }

    /**
     * Convert single condition
     *
     * @param string $condition
     * @return array
     * @throws \Exception
     */
    protected function convertSingleCondition($condition)
    {
        $condition = $this->parseCondition($condition);
        extract($condition);

        $typeParam = $this->getTypeParam($type);
        if (empty($typeParam)) {
            throw new \Exception("Can't find type param \"{$type}\".");
        }

        $ruleParam = [];
        foreach ($rules as $value) {
            $param = $this->getRuleParam($value);
            if (empty($param)) {
                $ruleParam['value'] = $value;
                break;
            }
            $ruleParam += $param;
        }
        if (count($ruleParam) != count($rules)) {
            throw new \Exception("Can't find all params. "
                . "\nSearch: " . implode(', ', $rules) . " "
                . "\nFind: " . implode(', ', $ruleParam)
            );
        }

        return $typeParam + $ruleParam;
    }

    /**
     * Get type param by name
     *
     * @param string $name
     * @return array
     */
    protected function getTypeParam($name) {
        return isset($this->mapTypeParams[$name]) ? $this->mapTypeParams[$name] : [];
    }

    /**
     * Get rule param by name
     *
     * @param string $name
     * @return array
     */
    protected function getRuleParam($name) {
        foreach ($this->mapRuleParams as $typeParam => &$params) {
            if (isset($params[$name])) {
                return [$typeParam => $params[$name]];
            }
        }
        return [];
    }

    /**
     * Decode value
     *
     * @param string $value
     * @return array
     * @throws \Exception
     */
    protected function decodeValue($value)
    {
        $value = str_replace('\{', '&lbrace;', $value);
        $value = str_replace('\}', '&rbrace;', $value);
        $value = str_replace('\[', '&lbracket;', $value);
        $value = str_replace('\]', '&rbracket;', $value);
        $value = str_replace('\:', '&colon;', $value);

        $value = preg_replace('/(\]|})({|\[)/', '$1,$2', $value);
        $value = preg_replace('/{([^:]+):/', '{"$1":', $value);
        $value = preg_replace('/\[([^\[{])/', '"$1', $value);
        $value = preg_replace('/([^\]}])\]/', '$1"', $value);

        $value = str_replace('&lbrace;', '{', $value);
        $value = str_replace('&rbrace;', '}', $value);
        $value = str_replace('&lbracket;', '[', $value);
        $value = str_replace('&rbracket;', ']', $value);
        $value = str_replace('&colon;', ':', $value);

        $value = "[{$value}]";
        $value = json_decode($value, true);
        if (null === $value) {
            throw new \Exception('Bad format value.');
        }
        return $value;
    }

    /**
     * Parse condition
     *
     * @param string $condition
     * @return array
     * @throws \Exception
     */
    protected function parseCondition($condition)
    {
        if (!preg_match_all('/([^|]+\|?)/', $condition, $match)) {
            throw new \Exception('Bad format condition');
        }
        foreach ($match[1] as $key => $value) {
            $match[1][$key] = rtrim($value, '|');
        }

        return [
            'type' => array_shift($match[1]),
            'rules' => array_values($match[1]),
        ];
    }
}
