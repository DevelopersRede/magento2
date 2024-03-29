<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\Source;
use Rede\Adquirencia\Gateway\Config\Config;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'rede';

    /**
     * @var Repository
     */
    protected $assetRepo;
    /**
     * @var Source
     */
    protected $assetSource;
    /**
     * @var ResolverInterface
     */
    private $localeResolver;
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Config $config
     * @param Repository $assetRepo
     * @param ResolverInterface $localeResolver
     * @param Source $assetSource
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        Repository $assetRepo,
        ResolverInterface $localeResolver,
        Source $assetSource,
        RequestInterface $request
    )
    {
        $this->config = $config;
        $this->assetRepo = $assetRepo;
        $this->localeResolver = $localeResolver;
        $this->assetSource = $assetSource;
        $this->request = $request;

    }

    /**
     * Retrieve assoc array of checkout configuration
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'environment' => $this->config->getEnvironment(),
                    'number_installments' => $this->config->getNumberInstallments(),
                    'min_total_installments' => $this->config->getMinTotalInstallments(),
                    '3ds_enabled' => $this->config->is3DSEnabled(),
                    'debit_enabled' => $this->config->isDebitEnabled(),
                    'rede' => $this->assetRepo->getUrl('Rede_Adquirencia::images/rede.jpg'),
                    'rede_off' => $this->assetRepo->getUrl('Rede_Adquirencia::images/rede-off.jpg'),
                    'years' => array_map(fn($year) => (object) [$year => $year],range(date('Y'), date('Y') + 15))

                ]
            ]
        ];
    }

    /**
     * Create a file asset that's subject of fallback system
     *
     * @param string $fileId
     * @param array $params
     *
     * @return File
     */
    public function createAsset($fileId, array $params = [])
    {
        $params = array_merge(['_secure' => $this->request->isSecure()], $params);
        return $this->assetRepo->createAsset($fileId, $params);
    }
}
