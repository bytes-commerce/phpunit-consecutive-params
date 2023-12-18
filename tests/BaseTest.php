<?php

declare(strict_types=1);

namespace SEEC\PhpUnit\Helper\Tests;

use PHPUnit\Framework\TestCase;
use SEEC\PhpUnit\Helper\ConsecutiveParams;

final class TestableClass
{
    public function foo(InputInterface $testClass): void
    {
        $testClass->setSomething(1);
        $testClass->setSomething(2);
    }
}

interface InputInterface
{
    public function setSomething(int $i): void;
}

final class BaseTest extends TestCase
{
    use ConsecutiveParams;

    public function test_it_can_run_a_test(): void
    {
        $mockClass = $this->getMockBuilder(InputInterface::class)
            ->onlyMethods(['setSomething'])
            ->getMock();
        $mockClass->expects($this->exactly(2))
            ->method('setSomething')
            ->with(...$this->withConsecutive(
                [1],
                [2]
            ));
        $testClass = new TestableClass();
        $testClass->foo($mockClass);
    }
}
