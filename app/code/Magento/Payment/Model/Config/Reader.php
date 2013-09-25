<?php
/**
 * Payment config reader
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Payment_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/payment/credit_cards/type' => 'code',
        '/payment/groups/group' => 'id',
    );
}
