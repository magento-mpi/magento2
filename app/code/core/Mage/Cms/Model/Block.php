<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CMS block model
 *
 * @method Mage_Cms_Model_Resource_Block _getResource()
 * @method Mage_Cms_Model_Resource_Block getResource()
 * @method Mage_Cms_Model_Block getTitle()
 * @method string setTitle(string $value)
 * @method Mage_Cms_Model_Block getIdentifier()
 * @method string setIdentifier(string $value)
 * @method Mage_Cms_Model_Block getContent()
 * @method string setContent(string $value)
 * @method Mage_Cms_Model_Block getCreationTime()
 * @method string setCreationTime(string $value)
 * @method Mage_Cms_Model_Block getUpdateTime()
 * @method string setUpdateTime(string $value)
 * @method Mage_Cms_Model_Block getIsActive()
 * @method int setIsActive(int $value)
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cms_Model_Block extends Mage_Core_Model_Abstract
{
    const CACHE_TAG     = 'cms_block';
    protected $_cacheTag= 'cms_block';

    protected function _construct()
    {
        $this->_init('cms/block');
    }
}
