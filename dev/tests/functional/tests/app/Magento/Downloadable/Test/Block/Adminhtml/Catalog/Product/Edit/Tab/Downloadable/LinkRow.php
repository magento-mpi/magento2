<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mtf\Block\Form;

class LinkRow extends Form
{
    /**
     * Update array for mapping
     *
     * @param array $fields
     * @return array
     */
    private function mappingUpdate(array $fields)
    {
        if ($fields['sample']['sample_type'] == 'url') {
            $fields['sample']['sample_type_url'] = 'Yes';
        } else {
            $fields['sample']['sample_type_file'] = 'Yes';
        }
        unset($fields['sample']['sample_type']);
        if ($fields['file_type'] == 'url') {
            $fields['file_type_url'] = 'Yes';
        } else {
            $fields['file_type_file'] = 'Yes';
        }
        unset($fields['file_type']);
        $mapping = $this->dataMapping($fields);
        return $mapping;
    }

    /**
     * Fill item link
     *
     * @param array $fields
     * @return void
     */
    public function fillLinks(array $fields)
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
    public function verifyLinks(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_verify($mapping);
    }

}
