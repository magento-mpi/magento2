<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Model\Authorizenet\Source;

/**
 * Authorize.net Payment CC Types Source Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return string[]
     */
    public function getAllowedTypes()
    {
        return ['VI', 'MC', 'AE', 'DI', 'OT'];
    }
}
