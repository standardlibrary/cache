<?php
declare(strict_types = 1);
/**
 * This file is part of the Standard Library Cache package.
 * For the full copyright information please view the LICENCE file that was
 * distributed with this package.
 *
 * @copyright Simon Deeley 2017
 */

namespace StandardLibrary;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;
use StandardLibrary\ImmutableObject;
use StandardLibrary\Exceptions\Cache\InvalidArgumentException;

/**
 * Standard Library implementation of the PSR-6 Cache standard
 *
 * Due to implementing from the PSR-6 interface we cannot use strong type-hinting
 * for some of the paramaters and return types so additional work has to be done
 * in-method to compensate for this.
 *
 * @author Simon Deeley <simondeeley@users.noreply.github.com>
 */
final class CacheItem extends ImmutableObject implements CacheItemInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var bool
     */
    protected $hit;

    /**
     * @var DateTimeInterface|null
     */
    protected $expires;

    /**
     * Creates a new CacheItem
     *
     * @param string $key - the unique key identifier of the item
     * @param mixed $data - the content to store
     * @param bool $hit - set to false if cache hit was a miss
     * @return void
     */
    final public function __construct(string $key, $data, bool $hit = null)
    {
        $this->key = $key;
        $this->data = $data;
        $this->hit = $hit === false ? false : null;
    }

    /**
     * Returns the key for the current cache item.
     *
     * @return string - The key string for this cache item.
     */
    final public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * @return mixed - The value corresponding to this cache item's key, or null if not found.
     */
    final public function get()
    {
        return $this->isHit() ? $this->data : null;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * @param mixed $value - The serializable value to be stored.
     * @return self
     */
    final public function set($value)
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return bool - True if the request resulted in a cache hit. False otherwise.
     */
    final public function isHit()
    {
        if (null !== $this->hit) {
            return $this->hit;
        }

        if ($this->expires instanceof DateTimeInterface) {

            // Compare expiry time to the time now
            return $this->hit = ($this->expires >= new DateTimeImmutable() ? true : false);

        } else {

            // If no expiry, then consider item valid
            return $this->hit = true;
        }
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration
     *  The point in time after which the item MUST be considered expired.
     * @return self
     */
    final public function expiresAt($expiration)
    {
        // A null value explicitly passed defaults to 'now'
        if (null === $time) {
            $expiration = new DateTimeImmutable();
        }

        // Convert to immutable date-time object
        if ($expiration instanceof DateTime) {
            $expiration = DateTimeImmutable::createFromMutable($expiration);

            $this->expires = $expiration;
        } else {
            throw new InvalidArgumentException(sprintf(
                'Value passed must be null or an object that implements DateTimeInterface, "%s" passed instead',
                gettype($time)
            ));
        }

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     * @return self
     */
    final public function expiresAfter($time)
    {
        // Assume an integer value is time in seconds
        if (is_int($time)) {
            $time = new DateInterval(sprintf('P%dS', $time));
        }

        if ($time instanceof DateInterval) {
            $now = new DateTime();

            $this->expiresAt($now->add($time));
        } else {
            throw new InvalidArgumentException(sprintf(
                'Value passed must be an integer or an instance of DateInterval, "%s" passed instead',
                gettype($time)
            ));
        }

        return $this;
    }

    /**
     * Returns a description of the object
     *
     * @static
     * @return string - Returns the name of the object type
     */
    public static function getType(): string
    {
        return 'CACHE_ITEM';
    }
}
