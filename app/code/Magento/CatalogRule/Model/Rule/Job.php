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
 * @method \Magento\CatalogRule\Model\Rule\Job setSuccess(string $errorMessage)
 * @method \Magento\CatalogRule\Model\Rule\Job setError(string $errorMessage)
 * @method string getSuccess()
 * @method string getError()
 * @method bool hasSuccess()
 * @method bool hasError()
 *
 * @category  Magento
 * @package   Magento_CatalogRule
 * @author    Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogRule\Model\Rule;

class Job extends \Magento\Object
{
    /**
     * Instance of event manager model
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * Basic object initialization
     *
     * @param \Magento\Core\Model\Event\Manager $eventManager
     */
    public function __construct(\Magento\Core\Model\Event\Manager $eventManager)
    {
        $this->_eventManager = $eventManager;
    }

    /**
     * Dispatch event "catalogrule_apply_all" and set success or error message depends on result
     *
     * @return \Magento\CatalogRule\Model\Rule\Job
     */
    public function applyAll()
    {
        try {
            $this->_eventManager->dispatch('catalogrule_apply_all');
            $this->setSuccess(__('The rules have been applied.'));
        } catch (\Magento\Core\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $this;
    }
}
