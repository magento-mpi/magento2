/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @package design
 * @category enterprise_default
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
 
if (!window.Enterprise) {
    window.Enterprise = {};
}

if (!Enterprise.CatalogEvent) {
    Enterprise.CatalogEvent = {};
}

Enterprise.CatalogEvent.Ticker = Class.create();

Object.extend(Enterprise.CatalogEvent.Ticker.prototype, {
    initialize: function (container, seconds) {
        this.container = $(container);
        this.seconds   = seconds;
        this.start     = new Date();
        this.interval = setInterval(this.applyTimer.bind(this), 1000);
        this.applyTimer();
    },
    getEstimate: function () {
        var now = new Date();
        
        var result = this.seconds - (now.getTime() - this.start.getTime())/1000;
        
        if (result < 0) {
            return 0;
        }
        
        return Math.round(result);
    },
    applyTimer: function () {
        var seconds = this.getEstimate();
        var hourSec = Math.floor(seconds / 3600) * 3600;
        var minuteSec =  Math.floor(seconds / 60) * 60;
        var secondSec = seconds;
        this.container.down('.hour').update(this.formatNumber(Math.floor(hourSec/3600)));
        this.container.down('.minute').update(this.formatNumber(Math.floor((minuteSec - hourSec)/60)));
        this.container.down('.second').update(this.formatNumber(seconds - minuteSec));
    },
    formatNumber: function (number) {
        if (number < 10) {
            return '0' + number.toString();
        }

        return number.toString();
    }
});
