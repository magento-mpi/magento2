<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class LinkRow
 * Fill link item data
 */
class LinkRow extends Form
{
    /**
     * Update array for mapping
     *
     * @param array $fields
     * @return array
     */
    public function _dataMapping(array $fields)
    {
        $fields['sample'][$fields['sample']['sample_type'] == 'url' ? 'sample_type_url' : 'sample_type_file'] = 'Yes';
        unset($fields['sample']['sample_type']);
        $fields[$fields['file_type'] == 'url' ? 'file_type_url' : 'file_type_file'] = 'Yes';
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
    public function fillLinkRow(array $fields)
    {
        $mapping = $this->_dataMapping($fields);
        $this->_fill($mapping);
    }

    /**
     * Get data item link
     *
     * @param array $fields
     * @return array
     */
    public function getDataLinkRow(array $fields)
    {
        $mapping = $this->_dataMapping($fields);
        return $this->_getData($mapping);
    }

}
