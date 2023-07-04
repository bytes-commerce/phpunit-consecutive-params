### PHPUnit Consecutive Parameters

After [PHPUnit has removed the possibility](https://github.com/sebastianbergmann/phpunit/issues/4026) to use `withConsecutive`, which was used by thousand of UnitTests, developers need a replacement which is not offered in a neat way at the moment.

Until this problem is solved directly in PHPUnit, this library offers a simple solution to use a replacement of `withConsecutive` again.

## Installation

```bash
$ composer require --dev seec/phpunit-consecutive-parameters
```

## Usage

```php
<?php

declare(strict_types=1);

namespace Your\Namespace\For\Tests;

use SEEC\PhpUnit\Helper\ConsecutiveParams;

final class TestRunnerContextTest extends TestCase
{
    use ConsecutiveParams;
    ...

    public function test_it_can_use_consecutive_replacement(): void
    {
        $mock = $this->createMock(\stdClass::class);
        $mock->expects($this->exactly(3))
            ->method('foo')
            ->with(...$this->consecutiveParams(
                ['a', 'b'],
                ['c', 'd'],
                ['e', 'f']
            ));
    }
```

Another example for automatic replacement in correctly formatted code:
```php 
->withConsecutive(
    ['a', 'b'],
    ['c', 'd'],
    ['e', 'f']
)
```

becomes

```php
->with(...$this->consecutiveParams(
    ['a', 'b'],
    ['c', 'd'],
    ['e', 'f']
))
