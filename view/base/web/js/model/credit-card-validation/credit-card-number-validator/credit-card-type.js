/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'mageUtils'
    ],
    function ($, utils) {
        'use strict';

        /**
         *   1. Amex - 15 caracteres | CVV 4 caracteres
         *   2. Banescard - 16 caracteres | CVV 3 caracteres
         *   3. Cabal - 16 caracteres | CVV 3 caracteres
         *   4. CredSystem - 16 caracteres | CVV 3 caracteres
         *   5. CREDZ - 16 caracteres | CVV 3 caracteres
         *   6. Diners - 14 caracteres | CVV 3 caracteres
         *   7. ELO - 16 caracteres | CVV 3 caracteres
         *   8. Hiper - 16 caracteres | CVV 3 caracteres
         *   9. Hipercard - 16 caracteres | CVV 3 caracteres
         *  10. JBC - 16 caracteres | CVV 3 caracteres
         *  11. Mastercard - 16 caracteres ou 19 | CVV 3 caracteres
         *  12. Sorocred - 16 caracteres | CVV 3 caracteres
         *  13. Visa - 16 caracteres | CVV 3 caracteres
         */
        var types = [
            {
                title: 'American Express',
                type: 'AMEX',
                pattern: '^3([47]\\d*)?$',
                isAmex: true,
                gaps: [4, 10],
                lengths: [15],
                code: {
                    name: 'CID',
                    size: 4
                }
            },

            {
                title: 'Banescard',
                type: 'BC',
                pattern: '\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },

            {
                title: 'Cabal',
                type: 'CA',
                pattern: '\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'CredSystem',
                type: 'CS',
                pattern: '\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'CREDZ',
                type: 'CZ',
                pattern: '\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'Diners',
                type: 'DN',
                pattern: '^(3(0[0-5]|095|6|[8-9]))\\d*$',
                gaps: [4, 10],
                lengths: [14, 16, 17, 18, 19],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },

            {
                title: 'Elo',
                type: 'ELO',
                pattern: '^(401178|401179|431274|438935|451416|457393|457631|457632|504175|627780|636297|636368|(506699|5067[0-6]\d|50677[0-8])|(50900\d|5090[1-9]\d|509[1-9]\d{2})|65003[1-3]|(65003[5-9]|65004\d|65005[0-1])|(65040[5-9]|6504[1-3]\d)|(65048[5-9]|65049\d|6505[0-2]\d|65053[0-8])|(65054[1-9]| 6505[5-8]\d|65059[0-8])|(65070\d|65071[0-8])|65072[0-7]|(65090[1-9]|65091\d|650920)|(65165[2-9]|6516[6-7]\d)|(65500\d|65501\d)|(65502[1-9]|6550[3-4]\d|65505[0-8]))[0-9]{10,12}',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'Hiper',
                type: 'HI',
                pattern: '^\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'Hipercard',
                type: 'HC',
                pattern: '^\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'JCB',
                type: 'JCB',
                pattern: '^35(2[8-9]|[3-8])\\d*$',
                gaps: [4, 8, 12],
                lengths: [16, 17, 18, 19],
                code: {
                    name: 'CVV',
                    size: 3
                }
            },

            {
                title: 'MasterCard',
                type: 'MC',
                pattern: '^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'Sorocred',
                type: 'SC',
                pattern: '^\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVC',
                    size: 3
                }
            },

            {
                title: 'Visa',
                type: 'VI',
                pattern: '^4\\d*$',
                gaps: [4, 8, 12],
                lengths: [16],
                code: {
                    name: 'CVV',
                    size: 3
                }
            }
        ];

        return {
            /**
             * Get Card type
             * @param {String} cardNumber
             * @returns {Array}
             */
            getCardTypes: function (cardNumber) {
                let i, value;
                let result = [];

                if (utils.isEmpty(cardNumber)) {
                    return result;
                }

                if (cardNumber === '') {
                    return $.extend(true, {}, types);
                }

                for (i = 0; i < types.length; i++) {
                    value = types[i];

                    if (new RegExp(value.pattern).test(cardNumber)) {
                        result.push($.extend(true, {}, value));
                    }
                }

                return result;
            }
        };
    }
);
