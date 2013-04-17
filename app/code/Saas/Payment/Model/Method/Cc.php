<?php
class Saas_Payment_Model_Method_Cc extends Mage_Payment_Model_Method_Cc
{
    /**
     * Forced payment disabling
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return false;
    }
}
