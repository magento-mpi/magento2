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
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

class Downloadable extends Tab
{
    const GROUP = 'product_info_tabs_downloadable_items';

    /**
     * 'Add New Row' button
     *
     * @var string
     */
    protected $addNewRow = '[data-action=add-link]';

    /**
     * Open tab
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        $element->find(static::GROUP, Locator::SELECTOR_ID)->click();
    }

    /**
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields['downloadable']['link'] as $index => $link) {
            $element->find($this->addNewRow)->click();
            $linkRowBlock = Factory::getBlockFactory()
                ->getMagentoDownloadableAdminhtmlCatalogProductEditTabDownloadableLinkRow($element);
            $linkRowBlock->fill($index, $link);
        }
    }
}
