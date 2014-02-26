<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element\Locator;

/**
 * Api tab of integration edit page.
 */
class Api extends Tab
{
    /**
     * Checks resources JStree visibility
     *
     * @return bool
     */
    public function isResourceVisible()
    {
        return $this->_rootElement->find($this->mapping['resources']['selector'])->isVisible();
    }

    /**
     * Change role access
     *
     * @param string $accessType
     */
    public function setRoleAccess($accessType)
    {
        $this->_rootElement->find(
            $this->mapping['resource_access']['selector'],
            Locator::SELECTOR_CSS,
            $this->mapping['resource_access']['input']
        )->setValue($accessType);
    }

    /**
     * Get role access
     *
     * @return string
     */
    public function getRoleAccess()
    {
        return $this->_rootElement->find(
            $this->mapping['resource_access']['selector'],
            Locator::SELECTOR_CSS,
            $this->mapping['resource_access']['input']
        )->getValue();
    }
}
