<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class Translate extends \Magento\Translate implements \Magento\TranslateInterface
{
    /**
     * @inheritdoc
     */
    public function init($area = null, $initParams = null, $forceReload = false)
    {
        parent::init($area, $initParams, $forceReload);
        $scope = null;
        if ($this->getConfig(self::CONFIG_KEY_AREA) == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            $scope = 'admin';
        }
        $this->_translateInline = $this->getInlineObject($initParams)->isAllowed($scope);
        return $this;
    }

}
