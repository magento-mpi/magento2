<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Widget\Instance\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CatalogEventWidgetForm
 * Widget Instance edit form
 */
class CatalogEventWidgetForm extends FormTabs
{
    /**
     * Fill form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);

        $this->fillTabs(['settings' => $tabs['settings']]);
        unset($tabs['settings']);
        $this->reinitRootElement();

        return $this->fillTabs($tabs, $element);
    }
}
