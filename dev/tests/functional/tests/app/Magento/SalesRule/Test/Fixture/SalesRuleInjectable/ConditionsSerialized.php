<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Fixture\SalesRuleInjectable;

use Mtf\Fixture\FixtureInterface;

/**
 * Class ConditionsSerialized
 * Source for conditions serialized
 */
class ConditionsSerialized implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var string
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Path to chooser grid class
     *
     * @var array
     */
    protected $chooserGrid = [
        'Customer Segment' => [
            'field' => 'name',
            'class' => 'Magento/CustomerSegment/Test/Block/Adminhtml/Customersegment/Grid/Chooser',
        ],
    ];

    /**
     * @param array $params
     * @param string $data
     */
    public function __construct(array $params, $data)
    {
        $this->params = $params;
        foreach ($this->chooserGrid as $conditionsType => $chooserGrid) {
            $data = preg_replace(
                '#(' . preg_quote($conditionsType) . '\|.*?\|)([^\d].*?)#',
                '${1}%' . $chooserGrid['class'] . '#' . $chooserGrid['field'] . '%${2}',
                $data
            );
        }
        $this->data = $data;
    }

    /**
     * Persist custom selections products
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data
     *
     * @param string|null $key [optional]
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
