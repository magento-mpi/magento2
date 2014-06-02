<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Edit
 * Form for creation of a Catalog Price Rule
 */
class Edit extends FormTabs
{
    /**
     * Fill form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @param array $replace
     * @return $this|FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null, array $replace = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        if ($replace) {
            $tabs = $this->prepareData($tabs, $replace);
        }
        foreach ($tabs as $tabName => $tabFields) {
            $tabElement = $this->getTabElement($tabName);
            $this->openTab($tabName);
            $tabElement->fillFormTab(array_merge($tabFields, $this->unassignedFields), $this->_rootElement);
            $this->updateUnassignedFields($tabElement);
        }
        if (!empty($this->unassignedFields)) {
            $this->fillMissedFields($tabs);
        }

        return $this;
    }

    /**
     * Replace placeholders in each values of data
     *
     * @param array $tabs
     * @param array $replace
     * @return array
     */
    protected function prepareData(array $tabs, array $replace)
    {
        foreach ($replace as $tabName => $fields) {
            foreach ($fields as $key => $pairs) {
                if (isset($tabs[$tabName][$key])) {
                    $tabs[$tabName][$key]['value'] = str_replace(
                        array_keys($pairs),
                        array_values($pairs),
                        $tabs[$tabName][$key]['value']
                    );
                }
            }
        }

        return $tabs;
    }
}