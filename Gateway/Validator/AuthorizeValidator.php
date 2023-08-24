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
        $response = array_merge(
            parent::getResponseValidators(),
            [
                function ($response) {
                    $responseCode = $response->getReturnCode();

                    return [
                        $responseCode === '00' || $responseCode === '220',
                        sprintf('[Rede %d] - %s', $response->getReturnCode(), $response->getReturnMessage())
                    ];
                }
            ]
        );


        return $response;
    }
}
