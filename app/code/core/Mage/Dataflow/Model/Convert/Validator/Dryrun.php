<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert dry run validator
 *
 * Insert where you want to step profile execution if dry run flag is set
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Validator_Dryrun extends Mage_Dataflow_Model_Convert_Validator_Abstract
{

    public function validate()
    {
        if ($this->getVar('dry_run') || $this->getProfile()->getDryRun()) {
            $this->addException(Mage::helper('Mage_Dataflow_Helper_Data')->__("Dry run set, stopping execution."), Mage_Dataflow_Model_Convert_Exceptin::FATAL);
        }
        return $this;
    }

}
