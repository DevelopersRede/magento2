/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'mageUtils',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator/luhn10-validator',
        'Rede_Adquirencia/js/model/credit-card-validation/credit-card-number-validator/credit-card-type'
    ],
    function (utils, luhn10, creditCardTypes) {
        'use strict';

        /**
         * Validation result wrapper
         * @param {Object} card
         * @param {Boolean} isPotentiallyValid
         * @param {Boolean} isValid
         * @returns {Object}
         */
        function resultWrapper(card, isPotentiallyValid, isValid) {
            return {
                card: card,
                isValid: isValid,
                isPotentiallyValid: isPotentiallyValid
            };
        }

        return function (value) {
            let potentialTypes,
                cardType,
                valid,
                i,
                maxLength;

            if (utils.isEmpty(value)) {
                return resultWrapper(null, false, false);
            }

            valid = luhn10(value);

            if (!valid || !/^\d{13,16}$/.test(value.replace(/[^\d]/g, ''))) {
                return resultWrapper(null, false, false);
            }

            return resultWrapper(value, valid, valid);
        };
    }
);
