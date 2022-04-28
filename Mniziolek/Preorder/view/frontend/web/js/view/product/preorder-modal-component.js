/*
 * @author Mniziolek Team
 * @copyright Copyright (c) Mniziolek (https://github.com/niziou/)
 */
require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function(
        $,
        modal
    ) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            buttons: [
                {
                    text: $.mage.__('Exit'),
                    class: 'pickup-button button primary',
                    click: function () {
                        this.closeModal();
                    }
                },
                {
                    text: $.mage.__('Place Order'),
                    class: 'pickup-button button primary preorder-submit',
                    click: function () {
                        $.ajax({
                            url : 'preorder/preorder/placepreorder',
                            dataType: 'json',
                            type: 'POST',
                            data : {
                                email: $('[name="email"]').val(),
                                firstname: $('[name="firstname"]').val(),
                                surname: $('[name="surname"]').val(),
                                street0: $('[name="street[0]"]').val(),
                                street1: $('[name="street[1]"]').val(),
                                city: $('[name="city"]').val(),
                                zip: $('[name="zip"]').val(),
                                country: $('[name="country"]').val(),
                                mobile: $('[name="mobile"]').val(),
                                product_id: $('[name="product_id"]').val()
                            },
                            success : function (response) {
                                if(response.status === 'success')
                                {
                                    $('#preorder-form').hide();
                                    $('#preorder-message').show().innerText = response.message;
                                }
                            },
                            error : function (response) {
                                alert({
                                    title : 'Error',
                                    content :'Error, Please try again !'.response.message
                                })
                                $('#preorder-message').show().innerText = response.message;
                            }
                        })
                    }
                }]
        };

        var popup = modal(options, $('#preorderModal'));
        $("#product-preorder-button").on('click',function(){
            $("#preorderModal").modal("openModal");
        });
    });
