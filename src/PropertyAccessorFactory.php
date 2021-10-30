<?php declare(strict_types = 1);

namespace WebChemistry\PropertyAccess;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

final class PropertyAccessorFactory
{

	public static function create(
		CacheItemPoolInterface $cacheItemPool,
		bool $magicGet = true,
		bool $magicSet = true,
		bool $magicCall = false,
		bool $exceptionOnInvalidIndex = false,
		bool $exceptionOnInvalidPropertyPath = true,
	): PropertyAccessorInterface
	{
		$builder = PropertyAccess::createPropertyAccessorBuilder();
		$builder->setCacheItemPool($cacheItemPool);
		$builder->setReadInfoExtractor(new ReflectionExtractor());
		$builder->setWriteInfoExtractor(new ReflectionExtractor());

		// exceptions
		if ($exceptionOnInvalidIndex) {
			$builder->enableExceptionOnInvalidIndex();
		}

		if (!$exceptionOnInvalidPropertyPath) {
			$builder->disableExceptionOnInvalidPropertyPath();
		}

		// magic
		if (!$magicGet) {
			$builder->disableMagicGet();
		}

		if (!$magicSet) {
			$builder->disableMagicSet();
		}

		if ($magicCall) {
			$builder->enableMagicCall();
		}

		return $builder->getPropertyAccessor();
	}

}
