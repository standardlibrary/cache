<?php
declare(strict_types=1);
/**
 * This file is part of the Standard Library Cache package.
 * For the full copyright information please view the LICENCE file that was
 * distributed with this package.
 *
 * @copyright Simon Deeley 2017
 */

use PHPUnit\Framework\TestCase;
use StandardLibrary\CacheItem;

/**
 * Test case for CacheItem
 *
 * @author Simon Deeley <simondeeley@users.noreply.github.com>
 * @uses StandardLibrary\CacheItem
 */
final class CacheItemTest extends TestCase
{
    /**
     * Test {@link CacheItem::get} and {@link CacheItem::getKey}
     *
     * @dataProvider dataProvider
     * @final
     * @param string $key
     * @param mixed $data
     * @return void
     */
    final public function testCorrectlyReturnsKeyAndData(string $key, $data): void
    {
        $item = new CacheItem($key, $data);

        $this->assertEquals($key, $item->getKey());
        $this->assertEquals($data, $item->get());
    }

    /**
     * Test returns data when setting TTL in future with {@link CacheItem::expiresAt}
     *
     * @dataProvider dataProvider
     * @final
     * @param string $key
     * @param mixed $data
     * @return void
     */
    final public function testFutureExpiresAt(string $key, $data): void
    {
        $date = new DateTime();
        $date->add($this->getDateInterval());

        $item = new CacheItem($key, $data);
        $item->expiresAt($date);

        $this->assertTrue($item->isHit());
        $this->assertEquals($data, $item->get());
    }

    /**
     * Test returns null when setting TTL in past with {@link CacheItem::expiresAt}
     *
     * @dataProvider dataProvider
     * @final
     * @param string $key
     * @param mixed $data
     * @return void
     */
    final public function testPastExpiresAt(string $key, $data): void
    {
        $date = new DateTime();
        $date->sub($this->getDateInterval());

        $item = new CacheItem($key, $data);
        $item->expiresAt($date);

        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    /**
     * Test returns data when setting TTL in future with {@link CacheItem::expiresAfter}
     *
     * @dataProvider dataProvider
     * @final
     * @param string $key
     * @param mixed $data
     * @return void
     */
    final public function testExpiresAfter(string $key, $data): void
    {
        $item = new CacheItem($key, $data);
        $item->expiresAfter(rand(10, PHP_INT_MAX));

        $this->assertTrue($item->isHit());
        $this->assertEquals($data, $item->get());
    }

    /**
     * dataProvider
     *
     * @return array
     */
    final public function dataProvider(): array
    {
        return [
            'String' => [
                'key',
                'value',
            ],

            'Integer' => [
                'key',
                rand(0,PHP_INT_MAX),
            ],

            'Boolean' => [
                'key',
                true,
            ],

            'null' => [
                'key',
                null,
            ],

            'Numeric array' => [
                'key',
                range(0, 10),
            ],

            'String array' => [
                'key',
                range('a', 'z')
            ],

            'Mixed array' => [
                'key',
                array_merge(range(0,10), range('a', 'z')),
            ],

            'Associative array' => [
                'key',
                array_combine(range('a', 'z'), range(1, 26)),
            ],

            'Large array' => [
                'key',
                range(0, 1000000),
            ],

            'Object' => [
                'key',
                new DateTime(),
            ],
        ];
    }

    /**
     * Get a random time duration
     *
     * @return DateInterval
     */
    private function getDateInterval(): DateInterval
    {
        return new DateInterval(sprintf('P%dD', rand(1, 355)));
    }
}
