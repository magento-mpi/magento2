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
    protected $resourceAccess = '#all';
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

    public function isResourceVisible()
    {
        return $this->_rootElement->find('[data-role="tree-resources-container"]')->isVisible();
    }

    public function changeRoleAccess($param)
    {
        $this->_rootElement->find($this->resourceAccess, Locator::SELECTOR_CSS, 'select')->setValue($param);
    }
}
