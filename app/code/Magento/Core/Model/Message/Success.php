<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Core_Model_Message_Success extends Magento_Core_Model_Message_Abstract
{
    public function __construct($code)
    {
        parent::__construct(Magento_Core_Model_Message::SUCCESS, $code);
    }
}
