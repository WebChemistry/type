<?php declare(strict_types = 1);

final class AssertBuilder
{

	/** @var array{string, string[], string[]}[] */
	private array $asserts;

	/** @var callable(string, string): void */
	private $acceptAssertion;

	/** @var callable(string, string): void */
	private $refuseAssertion;

	/** @var (callable(string): void)|null */
	private $before;

	/**
	 * @param callable(string, string): void $acceptAssertion
	 * @param callable(string, string): void $refuseAssertion
	 * @param (callable(string): void)|null $before
	 */
	public function __construct(callable $acceptAssertion, callable $refuseAssertion, ?callable $before = null)
	{
		$this->acceptAssertion = $acceptAssertion;
		$this->refuseAssertion = $refuseAssertion;
		$this->before = $before;
	}

	/**
	 * @param string[] $accepts
	 * @param string[] $refuses
	 */
	public function add(string $type, array $accepts, array $refuses): self
	{
		$this->asserts[] = [$type, $accepts, $refuses];

		return $this;
	}

	public function assert(): void
	{
		foreach ($this->asserts as [$type, $accepts, $refuses]) {
			if ($this->before) {
				($this->before)($type);
			}

			foreach ($accepts as $accept) {
				($this->acceptAssertion)($type, $accept);
			}

			foreach ($refuses as $refuse) {
				($this->refuseAssertion)($type, $refuse);
			}
		}
	}

}
