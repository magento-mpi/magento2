<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\GoogleShopping\Model;

/**
 * Google shopping synchronization operations flag
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Flag extends \Magento\Core\Model\Flag
{
    /**
     * Flag time to live in seconds
     */
    const FLAG_TTL = 72000;

    /**
     * Synchronize flag code
     *
     * @var string
     */
    protected $_flagCode = 'googleshopping';

    /**
     * Lock flag
     *
     * @return void
     */
    public function lock()
    {
        $this->setState(1)
            ->save();
    }

    /**
     * Check wheter flag is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return !!$this->getState() && !$this->isExpired();
    }

    /**
     * Unlock flag
     *
     * @return void
     */
    public function unlock()
    {
        $lastUpdate = $this->getLastUpdate();
        $this->loadSelf();
        $this->setState(0);
        if ($lastUpdate == $this->getLastUpdate()) {
            $this->save();
        }
    }

    /**
     * Check whether flag is unlocked by expiration
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!!$this->getState() && \Magento\GoogleShopping\Model\Flag::FLAG_TTL) {
            if ($this->getLastUpdate()) {
                return (time() > (strtotime($this->getLastUpdate()) + \Magento\GoogleShopping\Model\Flag::FLAG_TTL));
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
