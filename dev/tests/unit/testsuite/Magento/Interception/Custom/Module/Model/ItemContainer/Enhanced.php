<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\ItemContainer;

class Enhanced extends
\Magento\Interception\Custom\Module\Model\ItemContainer
{
    /**
     * @return string
     */
    public function getName()
    {
        return parent::getName() . '_enhanced';
    }
}
