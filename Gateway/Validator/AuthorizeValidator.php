<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Validator;

/**
 * Class AuthorizeValidator
 */
class AuthorizeValidator extends GeneralResponseValidator
{
    /**
     * @return array
     */
    protected function getResponseValidators()
    {
        return array_merge(
            parent::getResponseValidators(),
            [
                function ($response) {
                    return [
                        $response->getReturnCode() == '00',
                        sprintf('[Rede %d] - %s', $response->getReturnCode(), $response->getReturnMessage())
                    ];
                }
            ]
        );
    }
}
