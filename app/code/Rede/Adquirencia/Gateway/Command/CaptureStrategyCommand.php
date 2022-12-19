<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Command;

use Rede\Adquirencia\Gateway\Helper\SubjectReader;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Rede\Adquirencia\Model\Adapter\RedeAdapterFactory;
use Rede\Adquirencia\Model\Adapter\RedeSearchAdapter;

/**
 * Class CaptureStrategyCommand
 */
class CaptureStrategyCommand implements CommandInterface
{
    /**
     * Rede authorize and capture command
     */
    const SALE = 'sale';

    /**
     * Rede capture command
     */
    const CAPTURE = 'settlement';

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var RedeAdapterFactory
     */
    private $redeAdapterFactory;

    /**
     * @var RedeSearchAdapter
     */
    private $redeSearchAdapter;

    /**
     * Constructor
     *
     * @param CommandPoolInterface $commandPool
     * @param TransactionRepositoryInterface $repository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SubjectReader $subjectReader
     * @param RedeAdapterFactory $redeAdapterFactory ,
     * @param RedeSearchAdapter $redeSearchAdapter
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        TransactionRepositoryInterface $repository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SubjectReader $subjectReader,
        RedeAdapterFactory $redeAdapterFactory,
        RedeSearchAdapter $redeSearchAdapter
    )
    {
        $this->commandPool = $commandPool;
        $this->transactionRepository = $repository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->subjectReader = $subjectReader;
        $this->redeAdapterFactory = $redeAdapterFactory;
        $this->redeSearchAdapter = $redeSearchAdapter;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO */
        $paymentDO = $this->subjectReader->readPayment($commandSubject);

        $command = $this->getCommand($paymentDO);

        $this->commandPool->get($command)->execute($commandSubject);
    }

    /**
     * Get execution command name.
     *
     * @param PaymentDataObjectInterface $paymentDO
     * @return string
     */
    private function getCommand(PaymentDataObjectInterface $paymentDO)
    {
        $payment = $paymentDO->getPayment();

        if (empty($payment->getAdditionalInformation('Id Transação'))) {
            return self::SALE;
        }

        return self::CAPTURE;
    }
}
