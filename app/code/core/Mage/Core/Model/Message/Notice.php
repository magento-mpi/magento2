<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_Message_Notice extends Mage_Core_Model_Message_Abstract
{
    public function __construct($code)
    {
        parent::__construct(Mage_Core_Model_Message::NOTICE, $code);
    }
}
