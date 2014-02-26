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

/**
 * Class Downloadable
 *
 * @package Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab
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
     * Fill downloadable information
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (isset($fields['downloadable'])) {
            foreach ($fields['downloadable']['link'] as $index => $link) {
                $element->find($this->addNewRow)->click();
                $linkRowBlock = Factory::getBlockFactory()
                    ->getMagentoDownloadableAdminhtmlCatalogProductEditTabDownloadableLinkRow($element);
                $linkRowBlock->fill($index, $link);
            }
        }

        return $this;
    }
}
