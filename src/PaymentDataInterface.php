<?php
/**
 * Customer interface
 */

namespace Visualr\Omnipay\TillPayments;

/**
 * PaymentData interface
 *
 * This interface defines the functionality that all payment data in
 * the Till system are to have.
 */
interface PaymentDataInterface
{
    /**
     * IBAN data of the schedule
     */
    public function getIbanData();

    /**
     * Wallet data of the schedule
     */
    public function getWalletData();

    /**
     * Get data in payload format
     *
     * @return array
     */
    public function getData()
}
