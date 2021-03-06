<?php

declare(strict_types=1);

namespace SixtyEightPublishers\PoiBundle\Attribute\Value;

use Traversable;
use ArrayIterator;
use SixtyEightPublishers\PoiBundle\Exception\InvalidArgumentException;
use SixtyEightPublishers\PoiBundle\Attribute\Exception\AttributeValueException;

final class ArrayValueCollection implements ValueCollectionInterface
{
	/** @var array  */
	private $values;

	/** @var int  */
	private $state = self::STATE_NEW;

	/**
	 * @param array $values
	 */
	public function __construct(array $values = [])
	{
		$this->values = $values;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValue(string $name)
	{
		if (!array_key_exists($name, $this->values)) {
			throw AttributeValueException::missingValue($name);
		}

		return $this->values[$name];
	}

	/**
	 * {@inheritDoc}
	 */
	public function setValue(string $name, $value): void
	{
		if (self::STATE_MANAGED === $this->state && (!array_key_exists($name, $this->values) || $this->values[$name] !== $value)) {
			$this->state = self::STATE_UPDATED;
		}

		$this->values[$name] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator(bool $raw = FALSE): Traversable
	{
		return new ArrayIterator($this->values);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getState(): int
	{
		return $this->state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function changeState(int $state): void
	{
		if (!in_array($state, [self::STATE_NEW, self::STATE_MANAGED, self::STATE_UPDATED], TRUE)) {
			throw new InvalidArgumentException('Invalid state passed.');
		}

		$this->state = $state;
	}
}
