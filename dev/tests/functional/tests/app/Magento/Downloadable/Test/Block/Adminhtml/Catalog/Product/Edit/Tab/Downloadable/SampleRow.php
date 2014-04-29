<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mtf\Block\Form;

class SampleRow extends Form
{
    /**
     * Update array for mapping
     *
     * @param array $fields
     * @return array
     */
    private function mappingUpdate(array $fields)
    {
        if ($fields['type'] == 'url') {
            $fields['type_url'] = 'Yes';
        } else {
            $fields['type_file'] = 'Yes';
        }
        unset($fields['type']);
        $mapping = $this->dataMapping($fields);
        return $mapping;
    }

    /**
     * Fill item sample
     *
     * @param array $fields
     * @return void
     */
    public function fillSamples(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_fill($mapping);
    }

    /**
     * Verify item sample
     *
     * @param array $fields
     * @return void
     */
    public function verifySamples(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_verify($mapping);
    }
}
