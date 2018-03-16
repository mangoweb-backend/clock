<?php declare(strict_types = 1);

namespace Mangoweb\Clock;

use DateInterval;
use DateTimeImmutable;


class ClockMock
{
	public static function now(): DateTimeImmutable
	{
		return Clock::now();
	}


	public static function today(): DateTimeImmutable
	{
		return self::now()->setTime(0, 0, 0);
	}


	/**
	 * @param DateTimeImmutable|string $now
	 */
	public static function mockNow($now): void
	{
		if (is_string($now)) {
			$nowString = $now;
			foreach (['c', 'Y-m-d', 'Y-m-d H:i'] as $format) {
				$now = DateTimeImmutable::createFromFormat($format, $nowString, new \DateTimeZone('UTC'));
				if ($now instanceof DateTimeImmutable) {
					break;
				}
			}
		}

		if (!$now instanceof DateTimeImmutable) {
			throw new \LogicException();
		}

		Clock::refresh();
		Clock::$nowFactory = function () use ($now): DateTimeImmutable {
			return $now;
		};
	}


	/**
	 * @param  DateTimeImmutable|string $now
	 * @param  callable                 $callback
	 * @return mixed value return by invoking callback
	 */
	public static function mockNowScoped($now, callable $callback)
	{
		$before = self::now();

		try {
			self::mockNow($now);
			return $callback();

		} finally {
			self::mockNow($before);
		}
	}


	public static function addSeconds(int $seconds): void
	{
		if ($seconds < 0) {
			$seconds = (int) abs($seconds);
			self::mockNow(self::now()->sub(new DateInterval("PT{$seconds}S")));

		} else {
			self::mockNow(self::now()->add(new DateInterval("PT{$seconds}S")));
		}
	}


	public static function addHours(int $hours): void
	{
		if ($hours < 0) {
			$hours = (int) abs($hours);
			self::mockNow(self::now()->sub(new DateInterval("PT{$hours}H")));

		} else {
			self::mockNow(self::now()->add(new DateInterval("PT{$hours}H")));
		}
	}


	public static function addDays(int $days): void
	{
		if ($days < 0) {
			$days = (int) abs($days);
			self::mockNow(self::now()->sub(new DateInterval("P{$days}D")));

		} else {
			self::mockNow(self::now()->add(new DateInterval("P{$days}D")));
		}
	}
}
