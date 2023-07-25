<?php

namespace Tenko\Ai\Library;

use DateTime;
use DateTimeZone;

class Balance
{
    private string $accountBalance;

    private string $totalUsed;

    private string $remaining;

    private DateTime $accessUntil;

    /**
     * @param string $accountBalance
     * @param string $totalUsed
     * @param string $remaining
     * @param string $accessUntil
     * @param DateTimeZone|null $dateTimeZone
     * @throws \Exception
     */
    public function __construct(
        string $accountBalance = '',
        string $totalUsed = '',
        string $remaining = '',
        string $accessUntil = 'now',
        DateTimeZone $dateTimeZone = null
    ) {
        $this->accountBalance = $accountBalance;
        $this->totalUsed = $totalUsed;
        $this->remaining = $remaining;
        $this->accessUntil = new DateTime($accessUntil, $dateTimeZone);
    }

    /**
     * @return string
     */
    public function getAccountBalance(): string
    {
        return $this->accountBalance;
    }

    /**
     * @return string
     */
    public function getTotalUsed(): string
    {
        return $this->totalUsed;
    }

    /**
     * @return string
     */
    public function getRemaining(): string
    {
        return $this->remaining;
    }

    /**
     * @return DateTime
     */
    public function getAccessUntil(): DateTime
    {
        return $this->accessUntil;
    }


}