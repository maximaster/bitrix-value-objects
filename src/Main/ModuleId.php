<?php

declare(strict_types=1);

namespace Maximaster\BitrixValueObjects\Main;

use Stringable;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

/**
 * Идентификатор модуля.
 */
final class ModuleId implements Stringable
{
    private const BITRIX_VENDORNAME = 'bitrix';
    private const VENDOR_SEPARATOR = '.';

    /** @psalm-var non-empty-string */
    private string $moduleId;

    /** @psalm-var non-empty-string|null */
    private ?string $vendor = null;

    /**
     * Возвращает идентификатор главного модуля.
     *
     * @throws InvalidArgumentException
     */
    public static function main(): self
    {
        return new self('main');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $moduleId)
    {
        // https://academy.1c-bitrix.ru/education/?COURSE_ID=91&LESSON_ID=7513&LESSON_PATH=7507.7513
        Assert::stringNotEmpty($moduleId, 'Ожидалось, что идентификатор модуля не будет пустой строкой.');
        Assert::regex($moduleId, '/^[a-z]/', 'Имя модуля должно начинаться с латинской буквы в нижнем реестре. Получено: %s.');
        Assert::regex($moduleId, '/^[a-z0-9' . self::VENDOR_SEPARATOR . ']+$/', 'Имя модуля может содержать только латинские буквы в нижнем реестре, цифры и точки. Получено: %s.');

        $this->moduleId = $moduleId;
    }

    /**
     * Возвращает строковое представление идентификатора модуля.
     *
     * @psalm-return non-empty-string
     */
    public function __toString(): string
    {
        return $this->moduleId;
    }

    /**
     * Возвращает имя вендора модуля.
     *
     * @psalm-return non-empty-string
     */
    public function vendor(): string
    {
        if ($this->vendor === null) {
            [$vendor] = explode(self::VENDOR_SEPARATOR, $this->moduleId, 2);
            $this->vendor = ($vendor === $this->moduleId || $vendor === '') ? self::BITRIX_VENDORNAME : $vendor;
        }

        return $this->vendor;
    }

    /**
     * Является ли модуль системным?
     *
     * Не-системные модули отображаются в административной части в маркетплейсе.
     */
    public function isSystem(): bool
    {
        return str_contains($this->moduleId, self::VENDOR_SEPARATOR) === false;
    }

    /**
     * Является ли производителем модуля - Битрикс?
     */
    public function isBitrix(): bool
    {
        return $this->vendor() === self::BITRIX_VENDORNAME;
    }
}
