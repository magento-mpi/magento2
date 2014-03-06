<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento;

class Locale extends \Zend_Locale implements \Magento\LocaleInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($locale = null)
    {
        parent::__construct($locale);
    }
}