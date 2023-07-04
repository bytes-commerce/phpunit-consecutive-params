<?php

declare(strict_types=1);

namespace SEEC\PhpUnit\Helper;

use PHPUnit\Framework\TestCase;
use function array_column;
use function count;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\Constraint;

trait ConsecutiveParams
{
    public static function withConsecutive(array $firstCallArguments, array ...$consecutiveCallsArguments): iterable
    {
        foreach ($consecutiveCallsArguments as $consecutiveCallArguments) {
            TestCase::assertSameSize($firstCallArguments, $consecutiveCallArguments, 'Each expected arguments list need to have the same size.');
        }

        $allConsecutiveCallsArguments = [$firstCallArguments, ...$consecutiveCallsArguments];

        $numberOfArguments = count($firstCallArguments);
        $argumentList = [];
        for ($argumentPosition = 0; $argumentPosition < $numberOfArguments; ++$argumentPosition) {
            $argumentList[$argumentPosition] = array_column($allConsecutiveCallsArguments, $argumentPosition);
        }

        $mockedMethodCall = 0;
        $callbackCall = 0;
        foreach ($argumentList as $index => $argument) {
            yield new Callback(
                static function ($actualArgument) use ($argumentList, &$mockedMethodCall, &$callbackCall, $index, $numberOfArguments): bool {
                    $expected = $argumentList[$index][$mockedMethodCall] ?? null;

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
