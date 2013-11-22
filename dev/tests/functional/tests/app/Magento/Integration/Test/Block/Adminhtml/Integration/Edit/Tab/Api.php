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
     * Resource access dropdown
     *
     * @var string
     */
    protected $resourceAccess = '#all_resources';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->_mapping = array(
            'resource_access' => $this->resourceAccess,
        );
    }

    /**
     * Checks resources JStree visibility
     *
     * @return bool
     */
    public function isResourceVisible()
    {
        return $this->_rootElement->find('[data-role="tree-resources-container"]')->isVisible();
    }

    /**
     * Change role access
     *
     * @param string $accessType
     */
    public function setRoleAccess($accessType)
    {
        $this->_rootElement->find($this->resourceAccess, Locator::SELECTOR_CSS, 'select')->setValue($accessType);
    }

    /**
     * Verify role access
     */
    public function getRoleAccess()
    {
        return $this->_rootElement->find($this->resourceAccess, Locator::SELECTOR_CSS, 'select')->getValue();
    }
}
