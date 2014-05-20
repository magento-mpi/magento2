<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element as AbstractElement;

/**
 * Class ConditionsElement
 * Typified element class for conditions
 *
 * Format value.
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
class ConditionsElement extends AbstractElement
{
    /**
     * Main condition
     *
     * @var string
     */
    protected $mainCondition = './/ul[contains(@id,"__1__children")]/..';

    /**
     * Button add condition
     *
     * @var string
     */
    protected $addNew = './/*[contains(@class,"rule-param-new-child")]/a';

    /**
     * Button remote condition
     *
     * @var string
     */
    protected $remove = './/*/a[@class="rule-param-remove"]';

    /**
     * New condition
     *
     * @var string
     */
    protected $new = './ul/li/span[contains(@class,"rule-param-new-child")]/..';

    /**
     * Type of new condition
     *
     * @var string
     */
    protected $typeNew = './/*[@class="element"]/select';

    /**
     * Created condition
     *
     * @var string
     */
    protected $created = './/preceding-sibling::li[1]';

    /**
     * Children condition
     *
     * @var string
     */
    protected $children = './/ul[contains(@id,"conditions__")]';

    /**
     * Parameter of condition
     *
     * @var string
     */
    protected $param = './span[@class="rule-param"]/span/*[substring(@id,(string-length(@id)-%d+1))="%s"]/../..';

    /**
     * Key of last find param
     *
     * @var int
     */
    protected $findKeyParam = 0;

    /**
     * Map of parameters
     *
     * @var array
     */
    protected $mapParams = [
        'attribute',
        'operator',
        'value_type',
        'value',
        'aggregator',
    ];

    /**
     * Magento varienLoader.js loader
     *
     * @var string
     */
    protected $loader = './/ancestor::body/div[@id="loading-mask"]';

    /**
     * Set value to conditions
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $conditions = $this->decodeValue($value);
        $context = $this->find($this->mainCondition, Locator::SELECTOR_XPATH);
        $this->clear();
        $this->addConditions($conditions, $context);
    }

    /**
     * Add condition combination
     *
     * @param string $condition
     * @param Element $context
     * @return Element
     */
    protected function addConditionsCombination($condition, Element $context)
    {
        $condition = $this->parseCondition($condition);
        $newCondition = $context->find($this->new, Locator::SELECTOR_XPATH);
        $newCondition->find($this->addNew, Locator::SELECTOR_XPATH)->click();
        $typeNewCondition = $newCondition->find($this->typeNew, Locator::SELECTOR_XPATH, 'select');
        $typeNewCondition->setValue($condition['type']);
        $this->waitLoader();

        $createdCondition = $newCondition->find($this->created, Locator::SELECTOR_XPATH);
        if (!empty($condition['rules'])) {
            $this->fillCondition($condition['rules'], $createdCondition);
        }
        return $createdCondition;
    }

    /**
     * Add conditions
     *
     * @param array $conditions
     * @param Element $context
     * @return void
     */
    protected function addConditions(array $conditions, Element $context)
    {
        foreach ($conditions as $key => $condition) {
            $elementContext = is_numeric($key) ? $context : $this->addConditionsCombination($key, $context);
            if (is_string($condition)) {
                $this->addCondition($condition, $elementContext);
            } else {
                $this->addConditions($condition, $elementContext);
            }
        }
    }

    /**
     * Add single Condition
     *
     * @param string $condition
     * @param Element $context
     * @return void
     * @throws \Exception
     */
    protected function addCondition($condition, Element $context)
    {
        $condition = $this->parseCondition($condition);

        $newCondition = $context->find($this->new, Locator::SELECTOR_XPATH);
        $newCondition->find($this->addNew, Locator::SELECTOR_XPATH)->click();
        $newCondition->find($this->typeNew, Locator::SELECTOR_XPATH, 'select')->setValue($condition['type']);
        $this->waitLoader();

        $createdCondition = $newCondition->find($this->created, Locator::SELECTOR_XPATH);
        $this->fillCondition($condition['rules'], $createdCondition);
    }


    /**
     * Fill single condition
     *
     * @param array $rules
     * @param Element $element
     * @return void
     * @throws \Exception
     */
    protected function fillCondition(array $rules, Element $element)
    {
        $this->resetKeyParam();
        foreach ($rules as $key => $rule) {
            $param = $this->findNextParam($element);
            $param->find('a')->click();

            $value = $param->find('select', Locator::SELECTOR_CSS, 'select');
            if ($value->isVisible()) {
                $value->setValue($rule);
                continue;
            }
            $value = $param->find('input');
            if ($value->isVisible()) {
                $value->setValue($rule);

                $apply = $param->find('.//*[@class="rule-param-apply"]', Locator::SELECTOR_XPATH);
                if ($apply->isVisible()) {
                    $apply->click();
                }
                continue;
            }
            throw new \Exception('Undefined type of value ');
        }
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

    /**
     * Find next param of condition for fill
     *
     * @param Element $context
     * @return Element
     * @throws \Exception
     */
    protected function findNextParam(Element $context)
    {
        do {
            if (!isset($this->mapParams[$this->findKeyParam])) {
                throw new \Exception("Empty map of params");
            }
            $param = $this->mapParams[$this->findKeyParam];
            $element = $context->find(sprintf($this->param, strlen($param), $param), Locator::SELECTOR_XPATH);
            $this->findKeyParam += 1;
        } while (!$element->isVisible());

        return $element;
    }

    /**
     * Reset key of last find param
     *
     * @return void
     */
    protected function resetKeyParam()
    {
        $this->findKeyParam = 0;
    }

    /**
     * Wait loader
     *
     * @return void
     */
    protected function waitLoader()
    {
        $browser = $this;
        $loader = $this->loader;
        $browser->waitUntil(
            function () use ($browser, $loader) {
                $element = $browser->find($loader, Locator::SELECTOR_XPATH);
                return $element->isVisible() ? null : true;
            }
        );
    }

    /**
     * Clear conditions
     *
     * @return void
     */
    protected function clear()
    {
        $remote = $this->find($this->remove, Locator::SELECTOR_XPATH);
        while ($remote->isVisible()) {
            $remote->click();
            $remote = $this->find($this->remove, Locator::SELECTOR_XPATH);
        }
    }

    /**
     * Get value from conditions
     *
     * @return null
     */
    public function getValue()
    {
        return null;
    }
}
