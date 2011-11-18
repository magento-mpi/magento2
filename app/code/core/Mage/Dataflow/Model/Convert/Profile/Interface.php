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
 * Convert profile interface
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Dataflow_Model_Convert_Profile_Interface
{

    /**
     * Run current action
     *
     * @return Mage_Dataflow_Model_Convert_Profile_Abstract
     */
    public function run();

}
