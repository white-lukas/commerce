<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\commerce\models;

use craft\commerce\base\AddressZoneInterface;
use craft\commerce\base\Model;
use craft\commerce\Plugin;
use craft\commerce\records\ShippingZone as ShippingZoneRecord;
use craft\helpers\ArrayHelper;
use craft\helpers\UrlHelper;
use craft\validators\UniqueValidator;
use DateTime;
use yii\base\InvalidConfigException;

/**
 * Shipping zone model.
 *
 * @property Country[] $countries countries in this Shipping Zone
 * @property array $countryIds all states in this Shipping Zone
 * @property array $countriesNames the names of all countries in this Shipping Zone
 * @property string $cpEditUrl
 * @property bool $isCountryBased
 * @property State[] $states all states in this Shipping Zone
 * @property array $stateIds
 * @property array $statesNames the names of all states in this Shipping Zone
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class ShippingAddressZone extends Model implements AddressZoneInterface
{
    /**
     * @var int|null ID
     */
    public ?int $id = null;

    /**
     * @var string|null Name
     */
    public ?string $name = null;

    /**
     * @var string|null Description
     */
    public ?string $description = null;

    /**
     * @var bool Default
     */
    public bool $default = false;

    /**
     * @var string|null The code to match the zip code.
     * @since 2.2
     */
    public ?string $zipCodeConditionFormula = null;

    /**
     * @var DateTime|null
     * @since 3.4
     */
    public ?DateTime $dateCreated = null;

    /**
     * @var DateTime|null
     * @since 3.4
     */
    public ?DateTime $dateUpdated = null;

    /**
     * @var bool Country based
     */
    private bool $_isCountryBased = true;

    /**
     * @var Country[]|null
     */
    private ?array $_countries = null;

    /**
     * @var State[]|null
     */
    private ?array $_states = null;


    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('commerce/shipping/shippingzones/' . $this->id);
    }

    /**
     * @inheritdoc
     */
    public function getIsCountryBased(): bool
    {
        return $this->_isCountryBased;
    }

    public function setIsCountryBased(bool $value): bool
    {
        return $this->_isCountryBased = $value;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getCountryIds(): array
    {
        $countries = [];

        foreach ($this->getCountries() as $country) {
            $countries[] = $country->id;
        }

        return $countries;
    }

    /**
     * Returns all countries in this Shipping Zone.
     *
     * @throws InvalidConfigException
     */
    public function getCountries(): array
    {
        if ($this->_countries === null && $this->id) {
            $this->_countries = Plugin::getInstance()->getCountries()->getCountriesByShippingZoneId($this->id);
        }

        return $this->_countries ?? [];
    }

    /**
     * Sets countries in this Tax Zone.
     *
     * @param Country[] $countries
     */
    public function setCountries(array $countries): void
    {
        $this->_countries = $countries;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getStateIds(): array
    {
        return ArrayHelper::getColumn($this->getStates(), 'id');
    }

    /**
     * Returns all states in this Shipping Zone.
     *
     * @throws InvalidConfigException
     */
    public function getStates(): array
    {
        if ($this->_states === null && $this->id) {
            $this->_states = Plugin::getInstance()->getStates()->getStatesByShippingZoneId($this->id);
        }

        return $this->_states ?? [];
    }

    /**
     * @since 2.2
     */
    public function getZipCodeConditionFormula(): ?string
    {
        return $this->zipCodeConditionFormula;
    }

    /**
     * Set states in this shipping Zone.
     *
     * @param State[] $states
     */
    public function setStates(array $states): void
    {
        $this->_states = $states;
    }

    /**
     * Returns the names of all countries in this Shipping Zone.
     *
     * @throws InvalidConfigException
     */
    public function getCountriesNames(): array
    {
        return ArrayHelper::getColumn($this->getCountries(), 'name');
    }

    /**
     * Returns the names of all states in this Shipping Zone.
     *
     * @throws InvalidConfigException
     * @deprecated in 4.0. Use [[getStatesLabels]] instead.
     */
    public function getStatesNames(): array
    {
        return $this->getStatesLabels();
    }

    /**
     * Returns the labels of all states in this Shipping Zone.
     *
     * @throws InvalidConfigException
     */
    public function getStatesLabels(): array
    {
        return ArrayHelper::getColumn($this->getStates(), 'label');
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return [
            [['name'], 'required'],
            [['zipCodeConditionFormula'], 'string', 'length' => [1, 65000], 'skipOnEmpty' => true],
            [['name'], UniqueValidator::class, 'targetClass' => ShippingZoneRecord::class, 'targetAttribute' => ['name']],
            [
                ['states'],
                'required',
                'when' => static function($model) {
                    return !$model->isCountryBased;
                },
            ],
            [
                ['countries'],
                'required',
                'when' => static function($model) {
                    return $model->isCountryBased;
                },
            ],
        ];
    }
}
