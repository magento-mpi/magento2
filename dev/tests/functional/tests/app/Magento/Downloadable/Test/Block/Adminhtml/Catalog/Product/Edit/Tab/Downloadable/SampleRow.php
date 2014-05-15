<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mtf\Block\Form;

/**
 * Class SampleRow
 * Fill and get item sample data
 */
class SampleRow extends Form
{
    /**
     * Update array for mapping
     *
     * @param array $fields
     * @return array
     */
    public function _dataMapping(array $fields)
    {
        $fields[$fields['sample_type'] == 'url' ? 'sample_type_url' : 'sample_type_file'] = 'Yes';
        unset($fields['sample_type']);
        $mapping = $this->dataMapping($fields);
        return $mapping;
    }

    /**
     * Fill item sample
     *
     * @param array $fields
     * @return void
     */
    public function fillSampleRow(array $fields)
    {
        $mapping = $this->_dataMapping($fields);
        $this->_fill($mapping);
    }

    /**
     * Get data item sample
     *
     * @param array $fields
     * @return array
     */
    public function getDataSampleRow(array $fields)
    {
        $mapping = $this->_dataMapping($fields);
        return $this->_getData($mapping);
    }
}
