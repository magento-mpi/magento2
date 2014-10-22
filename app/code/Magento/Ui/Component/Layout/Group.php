<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout;

use Magento\Ui\Component\AbstractView;

/**
 * Class Group
 */
class Group extends AbstractView
{
    public function getIsRequired()
    {
        return $this->getData('required') ? 'required' : '';
    }
}
