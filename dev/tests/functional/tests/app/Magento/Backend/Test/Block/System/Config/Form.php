<?php
/**
 * Store configuration edit form
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block\System\Config;

use \Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use \Mtf\Factory\Factory;

class Form extends Block
{
    /**
     * Retrieve store configuration form group
     *
     * @param string $name
     * @return Form\Group
     */
    public function getGroup($name)
    {
        $blockFactory = Factory::getBlockFactory();
        $element = $this->_rootElement->find(
            '//legend[contains(text(), "' . $name . '")]/../..', Locator::SELECTOR_XPATH
        );
        return $blockFactory->getMagentoBackendSystemConfigFormGroup($element);
    }
} 