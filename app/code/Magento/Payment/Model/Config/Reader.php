<?php
/**
 * Payment config reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Config;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = [
        '/payment/credit_cards/type' => 'id',
        '/payment/groups/group' => 'id',
        '/payment/methods/method' => 'name',
    ];
}
