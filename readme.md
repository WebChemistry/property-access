## Installation

```neon
extensions:
	propertyAccess: WebChemistry\PropertyAccess\DI\PropertyAccessExtension

propertyInfo:
	magicGet: false # enable/disable magic __get, default: true
	magicSet: false # enable/disable magic __set, default: true
	magicCall: false # enable/disable magic __call, default: false
	exceptionOnInvalidIndex: false # enable/disable exception on invalid index, default: false
	exceptionOnInvalidPropertyPath: false # enable/disable exception on invalid property path, default: true
	cache: null # sets cache, default: FilesystemAdapter with "Symfony.PropertyAccess" namespace
```
