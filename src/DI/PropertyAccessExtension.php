<?php declare(strict_types = 1);

namespace WebChemistry\PropertyAccess\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use WebChemistry\PropertyAccess\PropertyAccessorFactory;

final class PropertyAccessExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'magicGet' => Expect::bool(true),
			'magicSet' => Expect::bool(true),
			'magicCall' => Expect::bool(false),
			'exceptionOnInvalidIndex' => Expect::bool(false),
			'exceptionOnInvalidPropertyPath' => Expect::bool(true),
			'cache' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->nullable()->default(
				FilesystemAdapter::class
			),
		]);
	}

	public function loadConfiguration(): void
	{
		/** @var stdClass $config */
		$config = $this->getConfig();

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('propertyInfoExtractor'))
			->setType(PropertyAccessorInterface::class)
			->setFactory(sprintf('%s::create(?, ?, ?, ?, ?, ?);', PropertyAccessorFactory::class), [
				$this->createCache(),
				$config->magicGet,
				$config->magicSet,
				$config->magicCall,
				$config->exceptionOnInvalidIndex,
				$config->exceptionOnInvalidPropertyPath,
			]);
	}

	private function createCache(): Statement
	{
		/** @var stdClass $config */
		$config = $this->getConfig();

		if ($config->cache instanceof Statement) {
			return $config->cache;

		} elseif ($config->cache === FilesystemAdapter::class) {
			$builder = $this->getContainerBuilder();

			return new Statement(
				FilesystemAdapter::class,
				[
					'namespace' => 'Symfony.PropertyAccess',
					'directory' => $builder->parameters['tempDir'] . '/cache',
				]
			);

		} elseif (is_string($config->cache)) {
			return new Statement($config->cache);

		}

		return new Statement(ArrayAdapter::class);
	}

}
