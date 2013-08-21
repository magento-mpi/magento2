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
 * @method Magento_CatalogRule_Model_Rule_Job setSuccess(string $errorMessage)
 * @method Magento_CatalogRule_Model_Rule_Job setError(string $errorMessage)
 * @method string getSuccess()
 * @method string getError()
 * @method bool hasSuccess()
 * @method bool hasError()
 *
 * @category  Magento
 * @package   Magento_CatalogRule
 * @author    Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogRule_Model_Rule_Job extends Magento_Object
{
    /**
     * Instance of event manager model
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Basic object initialization
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(Magento_Core_Model_Event_Manager $eventManager)
    {
        $this->_eventManager = $eventManager;
    }

    /**
     * Dispatch event "catalogrule_apply_all" and set success or error message depends on result
     *
     * @return Magento_CatalogRule_Model_Rule_Job
     */
    public function applyAll()
    {
        try {
            $this->_eventManager->dispatch('catalogrule_apply_all');
            $this->setSuccess(__('The rules have been applied.'));
        } catch (Magento_Core_Exception $e) {
            $this->setError($e->getMessage());
        }
        return $this;
    }
}
