<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint;

use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleEdit;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTargetRuleForm
 */
class AssertTargetRuleForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = [
        'conditions_serialized',
        'actions_serialized',
    ];

    /**
     * Assert that displayed target rule data on edit page(backend) equals passed from fixture
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleEdit $targetRuleEdit
     * @param TargetRule $targetRule
     * @param CustomerSegment $customerSegment
     * @return void
     */
    public function processAssert(
        TargetRuleIndex $targetRuleIndex,
        TargetRuleEdit $targetRuleEdit,
        TargetRule $targetRule,
        CustomerSegment $customerSegment = null
    ) {
        $filter = [
            'name' => $targetRule->getName(),
        ];
        $replace = [
            'customer_segment_ids' => [
                '%customer_segment%' => $customerSegment->hasData() ? $customerSegment->getName() : '',
            ],
        ];

        $targetRuleIndex->open();
        $targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $targetRuleData = $this->prepareData($targetRule->getData(), $replace);
        $formData = $targetRuleEdit->getTargetRuleForm()->getData($targetRule);
        $dataDiff = $this->verify($targetRuleData, $formData);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'TargetRule data on edit page(backend) not equals to passed from fixture.'
            . "\nFailed values: " . implode(', ', $dataDiff)
        );
    }

    /**
     * Verify data in form equals to passed from fixture
     *
     * @param array $data
     * @param array $replace
     * @return array
     */
    protected function prepareData(array $data, array $replace)
    {
        foreach ($replace as $key => $pairs) {
            if (isset($data[$key])) {
                $data[$key] = str_replace(
                    array_keys($pairs),
                    array_values($pairs),
                    $data[$key]
                );
            }
        }
        return $data;
    }

    /**
     * Verify data in form equals to passed from fixture
     *
     * @param array $dataFixture
     * @param array $dataForm
     * @return array
     */
    protected function verify(array $dataFixture, array $dataForm)
    {
        $result = [];

        if (isset($dataFixture['from_date'])) {
            $dataFixture['from_date'] = strtotime($dataFixture['from_date']);
        }
        if (isset($dataFixture['to_date'])) {
            $dataFixture['to_date'] = strtotime($dataFixture['to_date']);
        }
        if (isset($dataForm['from_date'])) {
            $dataForm['from_date'] = strtotime($dataForm['from_date']);
        }
        if (isset($dataForm['to_date'])) {
            $dataForm['to_date'] = strtotime($dataForm['to_date']);
        }
        if (isset($dataFixture['customer_segment_ids']) && !is_array($dataFixture['customer_segment_ids'])) {
            $dataFixture['customer_segment_ids'] = (array)$dataFixture['customer_segment_ids'];
        }

        $dataFixture = array_diff_key($dataFixture, array_flip($this->skippedFields));
        foreach ($dataFixture as $key => $value) {
            if (!isset($dataForm[$key])) {
                $result[] = "\ntarget rule {$key} ia absent in form";
                continue;
            }
            if (is_array($value)) {
                $diff = array_diff($value, $dataForm[$key]);
                if (empty($diff)) {
                    continue;
                }

                $result[] = "\ntarget rule {$key}: \""
                    . implode(', ', $dataForm[$key])
                    . "\" instead of \""
                    . implode(', ', $value)
                    . "\"";
                continue;
            }
            if ($value != $dataForm[$key]) {
                $result[] = "\ntarget rule{$key}: \"{$dataForm[$key]}\" instead of \"{$value}\"";
            }
        }

        return $result;
    }

    /**
     * Text success verify Target Rule form
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed target rule data on edit page(backend) equals to passed from fixture.';
    }
}
