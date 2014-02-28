<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usps\Block\Rma\Adminhtml\Rma\Edit\Tab\General\Shipping\Packaging;

use Magento\App\RequestInterface;
use Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Packaging;
use Magento\Usps\Helper\Data as UspsHelper;
use Magento\Usps\Model\Carrier;

/**
 * Rma block plugin
 */
class Plugin
{
    /**
     * Usps helper
     *
     * @var \Magento\Usps\Helper\Data
     */
    protected $uspsHelper;

    /**
     * Request
     *
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * Construct
     *
     * @param \Magento\Usps\Helper\Data $uspsHelper
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(UspsHelper $uspsHelper, RequestInterface $request)
    {
        $this->uspsHelper = $uspsHelper;
        $this->request = $request;
    }

    /**
     * Add rule to isGirthAllowed() method
     *
     * @param \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Packaging $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsGirthAllowed(Packaging $subject, $result)
    {
        return $result && $this->uspsHelper->displayGirthValue($this->request->getParam('method'));
    }

    /**
     * Add rule to isGirthAllowed() method
     *
     * @param \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Packaging $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundCheckSizeAndGirthParameter(Packaging $subject, \Closure $proceed)
    {
        $carrier = $subject->getCarrier();
        $regular = $subject->getShippingCarrierUspsSourceSize();

        $girthEnabled = false;
        $sizeEnabled = false;
        if ($carrier && isset($regular[0]['value'])) {
            if ($regular[0]['value'] == Carrier::SIZE_LARGE
                && in_array(
                    key($subject->getContainers()),
                    array(
                        Carrier::CONTAINER_NONRECTANGULAR,
                        Carrier::CONTAINER_VARIABLE,
                    )
                )
            ) {
                $girthEnabled = true;
            }

            if (in_array(
                key($subject->getContainers()),
                array(
                    Carrier::CONTAINER_NONRECTANGULAR,
                    Carrier::CONTAINER_RECTANGULAR,
                    Carrier::CONTAINER_VARIABLE,
                )
            )) {
                $sizeEnabled = true;
            }
        }

        return array($girthEnabled, $sizeEnabled);
    }
}
