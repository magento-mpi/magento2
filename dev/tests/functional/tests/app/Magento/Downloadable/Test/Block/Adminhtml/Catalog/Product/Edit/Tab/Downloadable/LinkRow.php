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
     * @param array $fields
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
     * @param array $fields
     */
    public function fillLinks(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_fill($mapping);
    }

    /**
     * @param array $fields
     */
    public function verifyLinks(array $fields)
    {
        $mapping = $this->mappingUpdate($fields);
        $this->_verify($mapping);
    }

}
