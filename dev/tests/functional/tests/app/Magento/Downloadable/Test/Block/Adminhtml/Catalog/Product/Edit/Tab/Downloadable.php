<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Downloadable
 * Fill and get data downloadable tab
 */
class Downloadable extends Tab
{
    /**
     * 'Add New Row' button
     *
     * @var string
     */
    protected $addNewRow = '[data-action=add-link]';

    /**
     * Downloadable block
     *
     * @var string
     */
    protected $downloadableBlock = '//dl[@id="tab_content_downloadableInfo"]';

    /**
     * Get Downloadable block
     *
     * @param string $type
     * @param Element $element
     * @return \Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples | \Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links
     */
    public function getDownloadableBlock($type, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\\' . $type,
            array('element' => $element->find($this->downloadableBlock, Locator::SELECTOR_XPATH))
        );
    }

    /**
     * Get data to fields on downloadable tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $_fields = [];
        if (isset($fields['downloadable_sample']['value'])) {
            $_fields['downloadable_sample'] = $this->getDownloadableBlock('Samples')->getDataSamples(
                $fields['downloadable_sample']['value']
            );
        }
        if (isset($fields['downloadable_links']['value'])) {
            $_fields['downloadable_links'] = $this->getDownloadableBlock('Links')->getDataLinks(
                $fields['downloadable_links']['value']
            );
        }

        return $_fields;
    }

    /**
     * Fill downloadable information
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['downloadable_sample']['value'])) {
            $this->getDownloadableBlock('Samples')->fillSamples($fields['downloadable_sample']['value']);
        }
        if (isset($fields['downloadable_links']['value'])) {
            $this->getDownloadableBlock('Links')->fillLinks($fields['downloadable_links']['value']);
        }

        /* for old test */
        if (isset($fields['downloadable'])) {
            foreach ($fields['downloadable']['link'] as $index => $link) {
                $element->find($this->addNewRow)->click();
                $linkRowBlock = Factory::getBlockFactory()
                    ->getMagentoDownloadableAdminhtmlCatalogProductEditTabDownloadableLinksRow($element);
                $linkRowBlock->fill($index, $link);
            }
        }

        return $this;
    }
}
