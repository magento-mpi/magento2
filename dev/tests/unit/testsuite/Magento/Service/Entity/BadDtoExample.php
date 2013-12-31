<?php
/**
 * Sample bad DTO implementation for testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

class BadDtoExample extends AbstractDto
{
    public function __construct()
    {
        // Intentionally not calling parent::__construct();
    }

    public function setData($data)
    {
        $this->_data = $data;
    }
}
