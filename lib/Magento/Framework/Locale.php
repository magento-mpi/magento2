<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

class Locale extends \Zend_Locale implements \Magento\Framework\LocaleInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($locale = null)
    {
        parent::__construct($locale);
    }
}
