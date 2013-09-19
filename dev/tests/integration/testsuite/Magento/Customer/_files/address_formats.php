<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$configXml = <<<EOD
<config>
    <global>
        <customer>
            <address>
                <formats>
                    <escaped_one translate="title">
                        <title>ESCAPED_ONE</title>
                        <escapeHtml>true</escapeHtml>
                    </escaped_one>
                    <escaped_two translate="title">
                        <title>ESCAPED_TWO</title>
                        <escapeHtml>no</escapeHtml>
                    </escaped_two>
                    <escaped_three translate="title">
                        <title>ESCAPED_THREE</title>
                        <escapeHtml>false</escapeHtml>
                    </escaped_three>
                    <escaped_four translate="title">
                        <title>ESCAPED_FOUR</title>
                        <escapeHtml>0</escapeHtml>
                    </escaped_four>
                    <escaped_five translate="title">
                        <title>ESCAPED_FIVE</title>
                        <escapeHtml></escapeHtml>
                    </escaped_five>
                    <escaped_six translate="title">
                        <title>ESCAPED_SIX</title>
                        <escapeHtml>1</escapeHtml>
                    </escaped_six>
                </formats>
            </address>
        </customer>
    </global>
</config>
EOD;

$config = Mage::getModel('Magento_Core_Model_Config_Base', array('sourceData' => $configXml));
/** @var $configModel Magento_Core_Model_Config */
$configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
$configModel->getNode()->extend($config->getNode());
