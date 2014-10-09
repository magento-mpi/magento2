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
 * @author    Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogRule\Model\Rule;

class Job extends \Magento\Framework\Object
{
    /**
     * @var \Magento\CatalogRule\Model\Indexer\Rule\RuleProcessor
     */
    protected $ruleProcessor;

    /**
     * Basic object initialization
     *
     * @param \Magento\CatalogRule\Model\Indexer\Rule\RuleProcessor $ruleProcessor
     */
    public function __construct(\Magento\CatalogRule\Model\Indexer\Rule\RuleProcessor $ruleProcessor)
    {
        $this->ruleProcessor = $ruleProcessor;
    }

    /**
     * Dispatch event "catalogrule_apply_all" and set success or error message depends on result
     *
     * @return \Magento\CatalogRule\Model\Rule\Job
     */
    public function applyAll()
    {
        try {
            $this->ruleProcessor->reindexAll();
            $this->setSuccess(__('The rules have been applied.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $this;
    }
}
