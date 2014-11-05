<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class WidgetForm
 * Widget Instance edit form
 */
class WidgetForm extends FormTabs
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
