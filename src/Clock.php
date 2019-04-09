<?php declare(strict_types = 1);

namespace Mangoweb\Clock;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;


class Clock
{
	/** @var null|callable */
	public static $nowFactory;

	/** @var null|DateTimeImmutable */
	private static $now;


	public static function now(): DateTimeImmutable
	{
		if (self::$now === null) {
			if (self::$nowFactory !== null) {
				self::$now = (self::$nowFactory)();

			} else {
				$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
				$now = $now->setTimestamp($now->getTimestamp()); // trim microseconds
				self::$now = $now;
			}
		}

		return self::$now;
	}


	public static function today(): DateTimeImmutable
	{
		return self::now()->setTime(0, 0, 0);
	}


	public static function tomorrow(): DateTimeImmutable
	{
		return self::addDays(1)->setTime(0, 0, 0);
	}


	public static function addSeconds(int $seconds): DateTimeImmutable
	{
		if ($seconds < 0) {
			$seconds = (int) abs($seconds);
			return self::now()->sub(new DateInterval("PT{$seconds}S"));

		} else {
			return self::now()->add(new DateInterval("PT{$seconds}S"));
		}
	}


	public static function addHours(int $hours): DateTimeImmutable
	{
		if ($hours < 0) {
			$hours = (int) abs($hours);
			return self::now()->sub(new DateInterval("PT{$hours}H"));

		} else {
			return self::now()->add(new DateInterval("PT{$hours}H"));
		}
	}


	public static function addDays(int $days): DateTimeImmutable
	{
		if ($days < 0) {
			$days = (int) abs($days);
			return self::now()->sub(new DateInterval("P{$days}D"));

		} else {
			return self::now()->add(new DateInterval("P{$days}D"));
		}
	}


	/**
	 * You should use this only in workers
	 */
	public static function refresh(): void
	{
		self::$now = null;
	}
}
