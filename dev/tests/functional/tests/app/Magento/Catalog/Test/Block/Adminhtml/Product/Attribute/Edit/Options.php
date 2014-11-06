<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\ObjectManager;
use Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Tab\Options\Option;

/**
 * Options element.
 */
class Options extends Element
{
    /**
     * 'Add Option' button
     *
     * @var string
     */
    protected $addOption = '#add_new_option_button';

    /**
     * Option form selector.
     *
     * @var string
     */
    protected $option = '.ui-sortable tr';

    /**
     * Set value.
     *
     * @param array|string $preset
     */
    public function setValue($preset)
    {
        foreach ($preset as $options) {
            $this->find($this->addOption)->click();
            $this->getFormInstance()->fillOptions($options);
        }
    }

    /**
     * Get value.
     *
     * @return string|void
     */
    public function getValue()
    {
        $data = [];
        $options = $this->find($this->option)->getElements();
        foreach ($options as $option) {
            $data[] = $this->getFormInstance($option)->getData();
        }
        return $data;
    }

    /**
     * Get options form.
     *
     * @param Element|null $element
     * @return Option
     */
    protected function getFormInstance(Element $element = null)
    {
        return ObjectManager::getInstance()->create(
            'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Tab\Options\Option',
            ['element' => $element === null ? $this->find($this->option . ':last-child') : $element]
        );
    }
}
