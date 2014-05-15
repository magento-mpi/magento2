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
 * Class Links
 * Fill links data
 */
class Links extends Form
{
    /**
     * 'Show Links block' button
     *
     * @var string
     */
    protected $showLinks = '//*[@id="dt-links"]/a';

    /**
     * 'Add New Row for links' button
     *
     * @var string
     */
    protected $addNewLinkRow = '//button[@id="add_link_item"]';

    /**
     * Downloadable link item block
     *
     * @var string
     */
    protected $downloadableLinkRowBlock = '//*[@id="link_items_body"]/tr[%d]';

    /**
     * Downloadable link title block
     *
     * @var string
     */
    protected $downloadableLinksTitle = "//*[@id='downloadable_links_title']";

    /**
     * Get Downloadable link item block
     *
     * @param int $index
     * @param Element $element
     * @return \Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\LinkRow
     */
    public function getDownloadableLinkRowBlock($index, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        return $this->blockFactory->create(
            'Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\LinkRow',
            array(
                'element' => $element->find(
                        sprintf($this->downloadableLinkRowBlock, ($index + 1)),
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
     * Fill links block
     *
     * @param array $fields
     * @param Element $element
     * @return void
     */
    public function fillLinks(array $fields, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        if (!$element->find($this->downloadableLinksTitle, Locator::SELECTOR_XPATH)->isVisible()) {
            $element->find($this->showLinks, Locator::SELECTOR_XPATH)->click();
        }
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping);
        foreach ($fields['downloadable']['link'] as $index => $link) {
            $element->find($this->addNewLinkRow, Locator::SELECTOR_XPATH)->click();
            $this->getDownloadableLinkRowBlock($index, $element)->fillLinkRow($link);
        }
    }

    /**
     * Get data links block
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataLinks(array $fields = null, Element $element = null)
    {
        $element = $element ? : $this->_rootElement;
        if (!$element->find($this->downloadableLinksTitle, Locator::SELECTOR_XPATH)->isVisible()) {
            $element->find($this->showLinks, Locator::SELECTOR_XPATH)->click();
        }
        $mapping = $this->dataMapping($fields);
        $_fields = $this->_getData($mapping);
        foreach ($fields['downloadable']['link'] as $index => $link) {
            $_fields['downloadable']['link'][$index] = $this->getDownloadableLinkRowBlock($index, $element)->getDataLinkRow($link);
        }
        return $_fields;
    }
}
