<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Widget\Instance\Edit;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Class WidgetForm
 * Widget Instance edit form
 */
class WidgetForm extends \Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\WidgetForm
{
    /**
     * Fill form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return WidgetForm
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
