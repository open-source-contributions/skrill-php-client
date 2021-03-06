<?php

declare(strict_types=1);

namespace Skrill\Tests\Request;

use PHPUnit\Framework\TestCase;
use Skrill\Request\PayoutRequest;
use Money\Currencies\ISOCurrencies;
use Skrill\ValueObject\Description;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Skrill\Exception\InvalidDescriptionException;
use Skrill\Exception\InvalidTransactionIDException;

/**
 * Class PayoutRequestTest.
 */
class PayoutRequestTest extends TestCase
{
    /**
     * @throws InvalidDescriptionException
     * @throws InvalidTransactionIDException
     */
    public function testSuccess()
    {
        $parser = new DecimalMoneyParser(new ISOCurrencies());

        $request = new PayoutRequest(
            $parser->parse('1000000.51', 'EUR'),
            new Description('Payout for Product ID:', '111')
        );

        self::assertEquals(
            [
                'currency' => 'EUR',
                'amount' => 1000000.51,
                'subject' => 'Payout for Product ID:',
                'note' => '111',
            ],
            $request->getPayload()
        );

        self::assertInstanceOf(
            PayoutRequest::class,
            $request->setSkrillOriginalTransactionId(new TransactionID('test'))
        );
        self::assertInstanceOf(PayoutRequest::class, $request->setReferenceTransaction(new TransactionID('test-ref')));

        self::assertEquals(
            [
                'currency' => 'EUR',
                'amount' => 1000000.51,
                'subject' => 'Payout for Product ID:',
                'note' => '111',
                'mb_transaction_id' => 'test',
                'frn_trn_id' => 'test-ref',
            ],
            $request->getPayload()
        );

        self::assertInstanceOf(PayoutRequest::class, $request->setOriginalTransactionId(new TransactionID('test-2')));

        self::assertEquals(
            [
                'currency' => 'EUR',
                'amount' => 1000000.51,
                'subject' => 'Payout for Product ID:',
                'note' => '111',
                'mb_transaction_id' => 'test',
                'frn_trn_id' => 'test-ref',
                'transaction_id' => 'test-2',
            ],
            $request->getPayload()
        );
    }
}
