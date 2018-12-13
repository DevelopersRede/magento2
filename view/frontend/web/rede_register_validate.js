/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/

define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
    'Magento_Payment/js/model/credit-card-validation/validator'
], function ($, mageTemplate, alert) {
    'use strict';

    $.widget('mage.rede_register_validate', {
        options: {
            context: null,
            placeOrderSelector: '[data-role="review-save"]',
            code: null
        },

        /**
         * {Function}
         * @private
         */
        _create: function () {
            this.element.validation();
            $('[data-container="' + this.options.code + '-cc-number"]').on('focusout', function () {
               $(this).valid();
            });
        },
    });

    // $.validation.creditCartTypes.set('ELO', [new RegExp('^(401178|401179|431274|438935|451416|457393|457631|457632|504175|627780|636297|636368|(506699|5067[0-6]\d|50677[0-8])|(50900\d|5090[1-9]\d|509[1-9]\d{2})|65003[1-3]|(65003[5-9]|65004\d|65005[0-1])|(65040[5-9]|6504[1-3]\d)|(65048[5-9]|65049\d|6505[0-2]\d|65053[0-8])|(65054[1-9]| 6505[5-8]\d|65059[0-8])|(65070\d|65071[0-8])|65072[0-7]|(65090[1-9]|65091\d|650920)|(65165[2-9]|6516[6-7]\d)|(65500\d|65501\d)|(65502[1-9]|6550[3-4]\d|65505[0-8]))[0-9]{10,12}'), new RegExp('^\d{3}$'), true]);
    // $.validation.creditCartTypes.set('AU', [new RegExp('^50[0-9]{14,17}$'), new RegExp('^\d{3}$'), true]);

    return $.mage.rede_register_validate;
});
