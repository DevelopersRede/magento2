<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rede\Adquirencia\Gateway\Config;

/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    public const KEY_ENVIRONMENT = 'environment';
    public const KEY_ACTIVE = 'active';
    public const KEY_PV = 'pv';
    public const KEY_TOKEN = 'token';
    public const KEY_SOFT_DESCRIPTOR = 'soft_descriptor';

    public const KEY_VERIFY_3DSECURE = 'verify_3dsecure';
    public const KEY_ENABLE_DEBIT = 'enable_debit';
    public const KEY_THRESHOLD_AMOUNT = 'threshold_amount';

    public const INSTALLMENTS = 'installments';
    public const NUMBER_INSTALLMENTS = 'number_installments';

    public const MIN_TOTAL_INSTALLMENTS = 'min_total_installments';

    public const KEY_MODULE = 'module';
    public const KEY_GATEWAY = 'gateway';

    public const VALUE_3DSECURE_ALL = 0;
    public const CODE_3DSECURE = 'three_d_secure';
    public const FRAUD_PROTECTION = 'fraudprotection';

    /**
     * Return the country specific card type config
     * @return array
     */
    public function getInstallments()
    {
        $installments = unserialize($this->getValue(self::INSTALLMENTS));

        return is_array($installments) ? $installments : [];
    }

    /**
     * Return the country specific card type config
     * @return float
     */
    public function getNumberInstallments()
    {
        return (double)$this->getValue(self::NUMBER_INSTALLMENTS);
    }

    /**
     * @return mixed
     */
    public function is3DSEnabled()
    {
        return (bool) $this->getValue(self::KEY_VERIFY_3DSECURE);
    }

    public function isDebitEnabled()
    {
        return (bool) $this->getValue(self::KEY_ENABLE_DEBIT);
    }

    /**
     * Return the country specific card type config
     * @return float
     */
    public function getMinTotalInstallments()
    {
        return (double)$this->getValue(self::MIN_TOTAL_INSTALLMENTS);
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->getValue(Config::KEY_MODULE);
    }

    /**
     * @return string
     */
    public function getGateway()
    {
        return $this->getValue(Config::KEY_GATEWAY);
    }

    /**
     * Check if 3d secure verification enabled
     * @return bool
     */
    public function isVerify3DSecure()
    {
        return (bool)$this->getValue(self::KEY_VERIFY_3DSECURE);
    }

    /**
     * Get threshold amount for 3d secure
     * @return float
     */
    public function getThresholdAmount()
    {
        return (double)$this->getValue(self::KEY_THRESHOLD_AMOUNT);
    }

    /**
     * @return string
     */
    public function getSoftDescriptor()
    {
        return $this->getValue(Config::KEY_SOFT_DESCRIPTOR);
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getValue(Config::KEY_ENVIRONMENT);
    }

    /**
     * @return string
     */
    public function getPv()
    {
        return $this->getValue(Config::KEY_PV);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getValue(Config::KEY_TOKEN);
    }

    /**
     * @return bool
     */
    public function hasFraudProtection()
    {
        return (bool)$this->getValue(Config::FRAUD_PROTECTION);
    }

    /**
     * Get Payment configuration status
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getValue(self::KEY_ACTIVE);
    }
}
