<?php
/**
 * Delete products from Google Content
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Items;

class MassDelete extends Refresh
{
    /**
     * Name of the operation to execute
     *
     * @var string
     */
    protected $operation = 'deleteItems';
}
