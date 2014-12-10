<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Model\Adminhtml;

/**
 * Customer balance model for backend
 */
class Balance extends \Magento\CustomerBalance\Model\Balance
{
    /**
     * Get website id
     *
     * @return int
     * @throws \Magento\Framework\Model\Exception
     */
    public function getWebsiteId()
    {
        if ($this->hasWebsiteId()) {
            return $this->_getData('website_id');
        }
        throw new \Magento\Framework\Model\Exception(__('A website ID must be set.'));
    }
}
