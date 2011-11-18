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
 * Convert action interface
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Dataflow_Model_Convert_Action_Interface
{

    /**
     * Run current action
     *
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function run();

}
