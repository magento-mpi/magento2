<?php
/**
 * Fixture for licenseAgreement method.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Checkout\Model\Agreement $agreement */
$agreement = Mage::getModel('Magento\Checkout\Model\Agreement');
$agreement->setData(
    array(
        'name' => 'Agreement name',
        'is_active' => '1',
        'is_html' => '0',
        'checkbox_text' => 'License text',
        'content' => 'Some content',
        'stores' => array(0)
    )
);
$agreement->save();
