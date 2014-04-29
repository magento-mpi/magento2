<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Driver\Selenium\Element as RootElement;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;
use Mtf\Client\Element\Locator;

/**
 * Class Downloadable
 *
 * @package Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab
 */
class Downloadable extends Tab
{
    /**
     * 'Add New Row for links' button
     *
     * @var string
     */
    protected $addNewLinkRow = '[data-action=add-link]';

    /**
     * 'Add New Row for samples' button
     *
     * @var string
     */
    protected $addNewSampleRow = '[data-action=add-sample]';

    /**
     * 'Show Sample block' button
     *
     * @var string
     */
    protected $showSample = '[data-ui-id=widget-accordion-1-samples-title-link]';

    /**
     * 'Show Links block' button
     *
     * @var string
     */
    protected $showLinks = '[data-ui-id=widget-accordion-1-links-title-link]';

    /**
     * 'link separately type' select
     *
     * @var string
     */
    protected $linkSeparately = "//*[@id='downloadable_link_purchase_type']";

    /**
     * Downloadable link item block
     *
     * @var string
     */
    protected $downloadableLinkBlock = '#link_items_body tr:nth-child(';

    /**
     * Downloadable sample item block
     *
     * @var string
     */
    protected $downloadableSampleBlock = '#sample_items_body tr:nth-child(';

    /**
     * Sample title block
     *
     * @var string
     */
    protected $downloadableSamplesTitle = "input[name='product[samples_title]']";

    /**
     * Downloadable title block
     *
     * @var string
     */
    protected $downloadableLinksTitle = "input[name='product[links_title]']";

    /**
     * links purchased separately select
     *
     * @var string
     */
    protected $downloadableLinksPurchasedSeparately = "[name='product[links_purchased_separately]']";

    /**
     * Get product row assigned to downloadable link
     *
     * @param int $blockNumber
     * @param Element $context
     * @return \Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\LinkRow
     */
    protected function getDownloadableLinkBlock($blockNumber, Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        return Factory::getBlockFactory()->getMagentoDownloadableAdminhtmlCatalogProductEditTabDownloadableLinkRow(
            $element->find($this->downloadableLinkBlock . $blockNumber . ')')
        );
    }

    /**
     * Get product row assigned to downloadable sample
     *
     * @param int $blockNumber
     * @param Element $context
     * @return \Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\SampleRow
     */
    protected function getDownloadableSampleBlock($blockNumber, Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        return Factory::getBlockFactory()->getMagentoDownloadableAdminhtmlCatalogProductEditTabDownloadableSampleRow(
            $element->find($this->downloadableSampleBlock . $blockNumber . ')')
        );
    }

    /**
     * Verify data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     *
     * @return bool
     */
    public function verifyFormTab(array $fields, Element $element)
    {
        if (isset($fields['downloadable_sample']['value'])) {
            if (!$element->find($this->downloadableSamplesTitle)->isVisible()) {
                $element->find($this->showSample)->click();
            }
            if ($element->find($this->downloadableSamplesTitle)->getValue(
                ) != $fields['downloadable_sample']['value']['title']
            ) {
                return false;
            }
            foreach ($fields['downloadable_sample']['value']['downloadable']['sample'] as $index => $sample) {
                $this->getDownloadableSampleBlock($index + 1)->verifySamples($sample);
            }
        }
        if (isset($fields['downloadable_links']['value'])) {
            if (!$element->find($this->downloadableLinksTitle)->isVisible()) {
                $element->find($this->showLinks)->click();
            }
            if ($element->find($this->downloadableLinksTitle)->getValue(
                ) != $fields['downloadable_links']['value']['title'] || $element->find(
                    $this->downloadableLinksPurchasedSeparately,
                    Locator::SELECTOR_CSS,
                    'select'
                )->getValue() !=
                $fields['downloadable_links']['value']['links_purchased_separately']
            ) {
                return false;
            }
            foreach ($fields['downloadable_links']['value']['downloadable']['link'] as $index => $link) {
                $this->getDownloadableLinkBlock($index + 1)->verifyLinks($link);
            }
        }
    }

    /**
     * Fill downloadable information
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (isset($fields['downloadable_sample']['value'])) {
            if (!$element->find($this->downloadableSamplesTitle)->isVisible()) {
                $element->find($this->showSample)->click();
            }
            $element->find($this->downloadableSamplesTitle)->setValue($fields['downloadable_sample']['value']['title']);
            foreach ($fields['downloadable_sample']['value']['downloadable']['sample'] as $index => $sample) {
                $element->find($this->addNewSampleRow)->click();
                $this->getDownloadableSampleBlock($index + 1)->fillSamples($sample);
            }
        }
        if (isset($fields['downloadable_links']['value'])) {
            if (!$element->find($this->downloadableLinksTitle)->isVisible()) {
                $element->find($this->showLinks)->click();
            }
            $element->find($this->downloadableLinksTitle)->setValue($fields['downloadable_links']['value']['title']);
            $element->find($this->downloadableLinksPurchasedSeparately, Locator::SELECTOR_CSS, 'select')->setValue(
                $fields['downloadable_links']['value']['links_purchased_separately']
            );
            foreach ($fields['downloadable_links']['value']['downloadable']['link'] as $index => $link) {
                $element->find($this->addNewLinkRow)->click();
                $this->getDownloadableLinkBlock($index + 1)->fillLinks($link);
            }
        }
        return $this;
    }
}
