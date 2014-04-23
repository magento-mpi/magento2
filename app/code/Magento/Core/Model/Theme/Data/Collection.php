<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme\Data;

use Magento\Framework\View\Design\Theme\ListInterface;

/**
 * Theme filesystem data collection
 */
class Collection extends \Magento\Core\Model\Theme\Collection implements ListInterface
{
    /**
     * Model of collection item
     *
     * @var string
     */
    protected $_itemObjectClass = 'Magento\Core\Model\Theme\Data';
}
