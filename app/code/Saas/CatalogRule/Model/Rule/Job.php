<?php
/**
 * CatalogRule Rule Job model
 *
 * Uses for encapsulate some logic of rule model and for having ability change behavior (for example, in controller)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Rule job model
 *
 * @category  Saas
 * @package   Saas_CatalogRule
 * @author    Magento Core Team <core@magentocommerce.com>
 */
class Saas_CatalogRule_Model_Rule_Job extends Mage_CatalogRule_Model_Rule_Job
{
    /**
     * Apply all price rules, invalidate related cache and refresh price index
     *
     * Override parent for changing success message
     *
     * @return Mage_CatalogRule_Model_Rule_Job
     */
    public function applyAll()
    {
        $parent = parent::applyAll();
        if ($parent->hasSuccess()) {
            $this->setSuccess($this->_helper->__('Task has been put into the queue.'));
        }
        return $this;
    }
}
