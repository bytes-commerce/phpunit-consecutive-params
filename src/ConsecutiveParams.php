<?php

declare(strict_types=1);

namespace SEEC\PhpUnit\Helper;

use function array_column;
use function count;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\TestCase;

trait ConsecutiveParams
{
    /** @return \Generator<int, Callback> */
    public static function withConsecutive(array $firstCallArguments, array ...$consecutiveCallsArguments): iterable
    {
        foreach ($consecutiveCallsArguments as $consecutiveCallArguments) {
            TestCase::assertSameSize($firstCallArguments, $consecutiveCallArguments, 'Each expected arguments list need to have the same size.');
        }

        $allConsecutiveCallsArguments = [$firstCallArguments, ...array_values($consecutiveCallsArguments)];

        $numberOfArguments = count($firstCallArguments);
        $argumentList = [];
        for ($argumentPosition = 0; $argumentPosition < $numberOfArguments; ++$argumentPosition) {
            $argumentList[$argumentPosition] = array_column($allConsecutiveCallsArguments, $argumentPosition);
        }

        $mockedMethodCall = 0;
        $callbackCall = 0;
        foreach ($argumentList as $argument) {
            yield new Callback(
                static function ($actualArgument) use (&$mockedMethodCall, &$callbackCall, $argument, $numberOfArguments): bool {
                    /** @var mixed $expected */
                    $expected = $argument[$mockedMethodCall] ?? null;

                    ++$callbackCall;
                    $mockedMethodCall = (int) ($callbackCall / $numberOfArguments);

                    if ($expected instanceof Constraint) {
                        TestCase::assertThat($actualArgument, $expected);
                    } else {
                        TestCase::assertEquals($expected, $actualArgument);
                    }

                    return true;
                },
            );
        }
    }
}
