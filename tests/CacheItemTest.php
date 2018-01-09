<?php
declare(strict_types=1);
/**
 * This file is part of the Standard Library Cache package.
 * For the full copyright information please view the LICENCE file that was
 * distributed with this package.
 *
 * @copyright Simon Deeley 2017
 */

use DateTime;
use DateTimeInterval;
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
        $item = new CacheItem($key, $data);
        $item->expiresAt($this->getFutureDate());

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
        $item = new CacheItem($key, $data);
        $item->expiresAt($this->getPastDate());

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
     * Get a random date in the future
     *
     * @return DateTime
     */
    private function getFutureDate(): DateTime
    {
        $date = new DateTime();
        $time = new DateInterval(sprintf('PT%dS', rand(10, PHP_INT_MAX)));
        $date->add($time);

        return $date;
    }

    /**
     * Get a random date in the past
     *
     * @return DateTime
     */
    private function getPastDate(): DateTime
    {
        $date = new DateTime();
        $time = new DateInterval(sprintf('PT%dS', rand(10, PHP_INT_MAX)));
        $date->sub($time);

        return $date;
    }
}
