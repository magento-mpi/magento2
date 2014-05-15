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
use Mtf\Client\Element\Locator;

/**
 * Class SampleRow *
 * Fill sample item data
 */
class Samples extends Form
{
    /**
     * 'Add New Row for samples' button
     *
     * @var string
     */
    protected $addNewSampleRow = '//button[@id="add_sample_item"]';

    /**
     * 'Show Sample block' button
     *
     * @var string
     */
    protected $showSample = '//dt[@id="dt-samples"]/a';

    /**
     * Sample title block
     *
     * @var string
     */
    protected $downloadableSamplesTitle = '//input[@name="product[samples_title]"]';

    /**
     * Downloadable sample item block
     *
     * @var string
     */
    protected $downloadableSampleRowBlock = '//*[@id="sample_items_body"]/tr[%d]';

    /**
     * Get Downloadable sample item block
     *
     * @param int $index
     * @param Element $element
     * @return \Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\SampleRow
     */
    public function getDownloadableSampleRowBlock($index, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\SampleRow',
            array(
                'element' => $element->find(
                        sprintf($this->downloadableSampleRowBlock, ($index + 1)),
                        Locator::SELECTOR_XPATH
                    )
            )
        );
    }

    /**
     * Update array for mapping
     *
     * @param array|null $fields
     * @param string $parent
     * @return array
     */
    public function dataMapping(array $fields = null, $parent = '')
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                unset($fields[$key]);
            }
        }
        $mapping = parent::dataMapping($fields);
        return $mapping;
    }

    /**
     * Fill samples block
     *
     * @param array|null $fields
     * @param Element $element
     * @return void
     */
    public function fillSamples(array $fields = null, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        if (!$element->find($this->downloadableSamplesTitle, Locator::SELECTOR_XPATH)->isVisible()) {
            $element->find($this->showSample, Locator::SELECTOR_XPATH)->click();
        }
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping);
        foreach ($fields['downloadable']['sample'] as $index => $sample) {
            $element->find($this->addNewSampleRow, Locator::SELECTOR_XPATH)->click();
            $this->getDownloadableSampleRowBlock($index, $element)->fillSampleRow($sample);
        }
    }

    /**
     * Get data samples block
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataSamples(array $fields = null, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        if (!$element->find($this->downloadableSamplesTitle, Locator::SELECTOR_XPATH)->isVisible()) {
            $element->find($this->showSample, Locator::SELECTOR_XPATH)->click();
        }
        $mapping = $this->dataMapping($fields);
        $_fields = $this->_getData($mapping);
        foreach ($fields['downloadable']['sample'] as $index => $sample) {
            $_fields['downloadable']['sample'][$index] = $this->getDownloadableSampleRowBlock($index, $element)->getDataSampleRow($sample);
        }
        return $_fields;
    }
}
