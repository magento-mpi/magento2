<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Address;

class Edit extends \Magento\Customer\Controller\Address
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('form');
    }
}
