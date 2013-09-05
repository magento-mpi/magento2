<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Payment_Model_Method_Purchaseorder extends Magento_Payment_Model_Method_Abstract
{
    protected $_code  = 'purchaseorder';
    protected $_formBlockType = 'Magento_Payment_Block_Form_Purchaseorder';
    protected $_infoBlockType = 'Magento_Payment_Block_Info_Purchaseorder';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Magento_Payment_Model_Method_Purchaseorder
     */
    public function assignData($data)
    {
        if (!($data instanceof \Magento\Object)) {
            $data = new \Magento\Object($data);
        }

        $this->getInfoInstance()->setPoNumber($data->getPoNumber());
        return $this;
    }
}
