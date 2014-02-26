<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Widget;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;

/**
 * Class FormTabs
 * Is used to represent any form with tabs on the page
 *
 * @package Magento\Backend\Test\Block\Widget
 */
class FormTabs extends Form
{
    /**
     * @var array
     */
    protected $tabClasses = array();

    /**
     * Fill form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tab => $tabFields) {
            $this->openTab($tab)->fillFormTab($tabFields, $this->_rootElement);
        }
        return $this;
    }

    /**
     * Verify form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @throws \Exception
     * @return FormTabs
     */
    public function verify(FixtureInterface $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tab => $tabFields) {
            if (!$this->openTab($tab)->verifyFormTab($tabFields, $this->_rootElement)) {
                throw new \Exception('Invalid form data.');
            }
        }
        return $this;
    }

    /**
     * Update form with tabs
     *
     * @param FixtureInterface $fixture
     * @return FormTabs
     */
    public function update(FixtureInterface $fixture)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tab => $tabFields) {
            $this->openTab($tab)->updateFormTab($tabFields, $this->_rootElement);
        }
        return $this;
    }

    /**
     * Create data array for filling tabs
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function getFieldsByTabs(FixtureInterface $fixture)
    {
        if ($fixture instanceof InjectableFixture) {
            $tabs = $this->getFixtureFieldsByTabs($fixture);
        } else {
            $tabs = $this->getFixtureFieldsByTabsDeprecated($fixture);
        }
        return $tabs;
    }

    /**
     * Create data array for filling tabs (new fixture specification)
     *
     * @param InjectableFixture $fixture
     * @return array
     */
    private function getFixtureFieldsByTabs(InjectableFixture $fixture)
    {
        $tabs = array();

        $data = $fixture->getData();
        foreach ($data as $field => $value) {
            $attributes = $fixture->getDataFieldConfig($field);
            if (!isset($attributes['group'])) {
                continue;
            }
            $attributes['value'] = $value;
            $tabs[$attributes['group']][$field] = $attributes;
        }
        return $tabs;
    }

    /**
     * Create data array for filling tabs (deprecated fixture specification)
     *
     * @param FixtureInterface $fixture
     * @return array
     * @deprecated
     */
    private function getFixtureFieldsByTabsDeprecated(FixtureInterface $fixture)
    {
        $tabs = array();

        $dataSet = $fixture->getData();
        $fields = isset($dataSet['fields']) ? $dataSet['fields'] : [];

        foreach ($fields as $field => $attributes) {
            if (!isset($attributes['group'])) {
                continue;
            }
            $tabs[$attributes['group']][$field] = $attributes;
        }
        return $tabs;
    }

    /**
     * Get tab element
     *
     * @param $tab
     * @return Tab
     * @throws \Exception
     */
    private function getTabElement($tab)
    {
        $tabRootElement = $this->_rootElement->find($tab, Locator::SELECTOR_ID);

        $tabClass = isset($this->tabClasses[$tab])
            ? $this->tabClasses[$tab]
            : '\Magento\Backend\Test\Block\Widget\Tab';
        /** @var $tabElement Tab */
        $tabElement = new $tabClass($tabRootElement);
        if (!$tabElement instanceof Tab) {
            throw new \Exception('Wrong Tab Class.');
        }

        return $tabElement;
    }

    /**
     * Open tab
     *
     * @param string $tabName
     * @return Tab
     */
    public function openTab($tabName)
    {
        $tabElement = $this->getTabElement($tabName);
        $tabElement->open($this->_rootElement);
        return $tabElement;
    }
}
