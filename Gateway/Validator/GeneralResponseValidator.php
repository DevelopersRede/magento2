<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rede\Adquirencia\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Rede\Adquirencia\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class GeneralResponseValidator extends AbstractValidator
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * Constructor
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(ResultInterfaceFactory $resultFactory, SubjectReader $subjectReader)
    {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $this->subjectReader->readResponseObject($validationSubject);

        $isValid = true;
        $errorMessages = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($response);

            if (!$validationResult[0]) {
                $isValid = $validationResult[0];

                if (is_array($validationResult[1])) {
                    $errorMessages = array_merge($errorMessages, $validationResult[1]);
                }
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }

    /**
     * @return array
     */
    protected function getResponseValidators()
    {
        return [
            function ($response) {
                return [
                    isset($response) ,
                    [__('Rede error response.')]
                ];
            }
        ];
    }
}
