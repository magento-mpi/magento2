<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Payment_Model_Method_Purchaseorder extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'purchaseorder';
    protected $_formBlockType = 'Mage_Payment_Block_Form_Purchaseorder';
    protected $_infoBlockType = 'Mage_Payment_Block_Info_Purchaseorder';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Method_Purchaseorder
     */
    public function assignData($data)
    {
        if (!($data instanceof Magento_Object)) {
            $data = new Magento_Object($data);
        }

        $this->getInfoInstance()->setPoNumber($data->getPoNumber());
        return $this;
    }
}
