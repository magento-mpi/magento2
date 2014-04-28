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
     * @param array $fields
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
     * @param array $fields
     */
    public function fillSamples(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_fill($mapping);
    }

    /**
     * @param array $fields
     */
    public function verifySamples(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_verify($mapping);
    }
}
