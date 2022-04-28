/*
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Mniziolek_Preorder/payment/preorder'
            },
            // add required logic here
        });
    }
);
