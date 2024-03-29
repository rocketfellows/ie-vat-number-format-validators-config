<?php

namespace rocketfellows\IEVatNumberFormatValidatorsConfig\tests\unit;

use arslanimamutdinov\ISOStandard3166\ISO3166;
use PHPUnit\Framework\TestCase;
use rocketfellows\CountryVatFormatValidatorInterface\CountryVatFormatValidatorInterface;
use rocketfellows\CountryVatFormatValidatorInterface\CountryVatFormatValidators;
use rocketfellows\CountryVatNumberFormatValidatorsConfig\CountryVatNumberFormatValidatorsConfigInterface;
use rocketfellows\IEVatFormatValidator\IEVatFormatValidator;
use rocketfellows\IEVatNumberFormatValidatorsConfig\IEVatNumberFormatValidatorsConfig;

class IEVatNumberFormatValidatorsConfigTest extends TestCase
{
    private const TESTING_CONFIG_CLASS = IEVatNumberFormatValidatorsConfig::class;
    private const EXPECTED_CONFIG_DEFAULT_VALIDATOR_CLASS = IEVatFormatValidator::class;

    public function testOverrideConfigurationDefaultValidator(): void
    {
        $newDefaultValidator = $this->createMock(CountryVatFormatValidatorInterface::class);

        $configClassName = self::TESTING_CONFIG_CLASS;
        $config = new $configClassName($newDefaultValidator);

        $actualValidators = [];

        foreach ($config->getValidators() as $validator) {
            $actualValidators[] = $validator;
        }

        $this->assertExpectedConfigCountry($config);
        $this->assertCount(1, $actualValidators);
        $this->assertNotInstanceOf(self::EXPECTED_CONFIG_DEFAULT_VALIDATOR_CLASS, $actualValidators[0]);
        $this->assertEquals($newDefaultValidator, $actualValidators[0]);
    }

    public function testDefaultConfigInitialization(): void
    {
        $configClassName = self::TESTING_CONFIG_CLASS;
        $config = new $configClassName();

        $actualValidators = [];

        foreach ($config->getValidators() as $validator) {
            $actualValidators[] = $validator;
        }

        $this->assertExpectedConfigCountry($config);
        $this->assertCount(1, $actualValidators);
        $this->assertInstanceOf(self::EXPECTED_CONFIG_DEFAULT_VALIDATOR_CLASS, $actualValidators[0]);
    }

    /**
     * @dataProvider getConfigValidators
     * @param CountryVatFormatValidatorInterface|null $defaultValidator
     * @param CountryVatFormatValidators|null $additionalValidators
     * @param CountryVatFormatValidatorInterface[] $expectedValidators
     */
    public function testConfigureConfigValidators(
        ?CountryVatFormatValidatorInterface $defaultValidator,
        ?CountryVatFormatValidators $additionalValidators,
        array $expectedValidators
    ): void {
        $configClassName = self::TESTING_CONFIG_CLASS;
        $config = new $configClassName($defaultValidator, $additionalValidators);

        $actualValidators = [];

        foreach ($config->getValidators() as $validator) {
            $actualValidators[] = $validator;
        }

        $this->assertExpectedConfigCountry($config);
        $this->assertEquals($expectedValidators, $actualValidators);
    }

    public function getConfigValidators(): array
    {
        $defaultValidator = $this->createMock(CountryVatFormatValidatorInterface::class);
        $validator = $this->createMock(CountryVatFormatValidatorInterface::class);

        return [
            'default validator null, additional validators null' => [
                'defaultValidator' => null,
                'additionalValidators' => null,
                'expectedValidators' => [
                    $this->getDefaultConfigValidator(),
                ],
            ],
            'default validator null, additional validators not null and empty' => [
                'defaultValidator' => null,
                'additionalValidators' => new CountryVatFormatValidators(),
                'expectedValidators' => [
                    $this->getDefaultConfigValidator(),
                ],
            ],
            'default validator null, additional validators not null and not empty' => [
                'defaultValidator' => null,
                'additionalValidators' => new CountryVatFormatValidators(
                    $validator,
                    $validator,
                    $validator
                ),
                'expectedValidators' => [
                    $this->getDefaultConfigValidator(),
                    $validator,
                    $validator,
                    $validator
                ],
            ],
            'default validator not null, additional validators null' => [
                'defaultValidator' => $defaultValidator,
                'additionalValidators' => null,
                'expectedValidators' => [
                    $defaultValidator,
                ],
            ],
            'default validator not null, additional validators not null and empty' => [
                'defaultValidator' => $defaultValidator,
                'additionalValidators' => new CountryVatFormatValidators(),
                'expectedValidators' => [
                    $defaultValidator,
                ],
            ],
            'default validator not null, additional validators not null and not empty' => [
                'defaultValidator' => $defaultValidator,
                'additionalValidators' => new CountryVatFormatValidators(
                    $validator,
                    $validator,
                    $validator
                ),
                'expectedValidators' => [
                    $defaultValidator,
                    $validator,
                    $validator,
                    $validator
                ],
            ],
        ];
    }

    private function assertExpectedConfigCountry(CountryVatNumberFormatValidatorsConfigInterface $config): void
    {
        $this->assertEquals(ISO3166::IE(), $config->getCountry());
    }

    private function getDefaultConfigValidator(): CountryVatFormatValidatorInterface
    {
        $defaultConfigValidatorClassName = self::EXPECTED_CONFIG_DEFAULT_VALIDATOR_CLASS;

        return new $defaultConfigValidatorClassName();
    }
}
