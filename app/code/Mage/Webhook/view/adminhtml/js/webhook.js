/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
function activateSubscription(url)
{
    $j = jQuery.noConflict();
    var activateSubscriptionDiv = $j('#activate-subscription');
    if (undefined === activateSubscriptionDiv[0]) {
        activateSubscriptionDiv = $j('<div id="activate-subscription"/>');
        $j('body').append(activateSubscriptionDiv);
    }
    activateSubscriptionDiv.html('');

    activateSubscriptionDiv.append('<div id="popup-window-mask"  style="width: 1665px; height: 719px;"/>');
    var modal = activateSubscriptionDiv.append('<div id="modal" class="popup-window-mask hide" style="height: 100%; width: 100%;"></div>');
    modal.append('<div class="sh hide"><div class="b"><div class="top"><a class="close" href="#">close</a></div><iframe id="ifr" frameborder="0"/></div></div>');
    // activateSubscriptionDiv.append('<div id="modal" class="wr hide"> <div class="sh"><div class="b"><a href="#" class="close"></a><iframe id="ifr" frameborder="0"/></div></div></div>');
    openLoaderPopup();

	$j('#ifr').on('load', function() {
        closeLoaderPopup();
        $j("#modal").removeClass("hide");
        $j("#activate-subscription > .sh").removeClass("hide");
    });
    $j('#ifr').attr('src', url);

    // Close Modal
    $j("#activate-subscription > .sh > .b").on("click","a.close",function(){
        $j("#modal").addClass("hide");
        $j("#activate-subscription > .sh").addClass("hide");
        return false;
    });
    /* Handle overlay */
    $j(window).on("resize",function(){
        var top = ($j(window).height() - $j("#activate-subscription > .sh").height()) / 2;
        var left = ($j(window).width() - $j("#activate-subscription > .sh").width()) / 2;
        /* Taking scrolling under consideration */
        if ($j(window).scrollTop()) {
            top += $j(window).scrollTop();
        }
        if ($j(window).scrollLeft()) {
            left += $j(window).scrollLeft();
        }
        if (top < 0) {
            top = 0;
        }
        if (left < 0) {
            left = 0;
        }
        $j("#activate-subscription > .sh").css("top", top + "px");
        $j("#activate-subscription > .sh").css("left", left + "px");
        $j("#modal ")
                .css("width",$j(document).width() + "px")
                .css("height",$j(document).height() + "px");
    });
    /* Handle scrolling */
    $j(document).scroll(function() {
        $j(window).trigger("resize");
    });
    /* Trigger initial resize to position loading box */
    $j(window).trigger("resize");
}

function openLoaderPopup()
{
    // Prototype code
    var height = $('html-body').getHeight();
    $('popup-window-mask').setStyle({'height':height + 'px'});
    Element.show('popup-window-mask');
    $('loading-mask').show();
}

function closeLoaderPopup()
{
    // Prototype code
    Element.hide('popup-window-mask');
    $('loading-mask').hide();
}

function closeIframe(url)
{
    document.location.href = url;
    return false
}

