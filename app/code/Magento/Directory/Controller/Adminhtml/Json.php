<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Json controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Controller\Adminhtml;

class Json extends \Magento\Backend\App\Action
{
    /**
     * Return JSON-encoded array of country regions
     *
     * @return string
     */
    public function countryRegionAction()
    {
        $arrRes = array();

        $countryId = $this->getRequest()->getParam('parent');
        if (!empty($countryId)) {
            $arrRegions = $this->_objectManager->create(
                'Magento\Directory\Model\Resource\Region\Collection'
            )->addCountryFilter(
                $countryId
            )->load()->toOptionArray();

            if (!empty($arrRegions)) {
                foreach ($arrRegions as $region) {
                    $arrRes[] = $region;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($arrRes)
        );
    }
}
