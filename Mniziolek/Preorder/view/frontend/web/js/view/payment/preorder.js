/*
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        var config = window.checkoutConfig.payment,
            preorder = 'preorder'

        if (config[preorder].isActive) {
            rendererList.push(
                {
                    type: 'preorder',
                    component: 'Mniziolek_Preorder/js/view/payment/method-renderer/preorder'
                },
                // other payment method renderers if required
            );
        }
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
