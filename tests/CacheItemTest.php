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
    final public function testReturnsKey(string $key, $data): void
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
            'Simple data' => [
                'a' => 'foo',
                'b' => 1234,
                'c' => [1, 2, 3, 'a', 'b', 'c'],
                'd' => null,
                'e' => true,
                'f' => false,
                'g' => new stdClass(),
                'h' => new DateTime(),
            ]
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
