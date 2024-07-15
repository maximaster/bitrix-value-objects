<?php

declare(strict_types=1);

use Maximaster\BitrixValueObjects\Main\ModuleId;

describe(ModuleId::class, function (): void {
    describe('__construct', function (): void {
        it('должен выдавать ошибку, если формат некорректен', function (): void {
            foreach (['%error', '1module', 'SUPER'] as $wrongId) {
                expect(fn () => new ModuleId($wrongId))
                    ->toThrow(
                        sprintf(
                            'Имя модуля должно начинаться с латинской буквы в нижнем реестре. Получено: "%s".',
                            $wrongId
                        )
                    );
            }

            foreach (['my.BITRIX', 'my%bitrix'] as $wrongId) {
                expect(fn () => new ModuleId($wrongId))
                    ->toThrow(
                        sprintf(
                            'Имя модуля может содержать только латинские буквы в нижнем реестре, цифры и точки. Получено: "%s".',
                            $wrongId
                        )
                    );
            }
        });
    });

    describe('::vendor', function (): void {
       it('должен быть bitrix, если не указан явным образом', function (): void {
           $id = new ModuleId('hello');
           expect($id->vendor())->toBe('bitrix');
       });

       it('должен определяться, если указан явным образом', function (): void {
           $vendor = 'my';
           $id = new ModuleId("$vendor.hello");
           expect($id->vendor())->toBe($vendor);
       });
    });

    describe('::isSystem', function (): void {
        it('должен быть положительным, если вендор не указан явным образом', function (): void {
            $moduleId = new ModuleId('sale');
            expect($moduleId->isSystem())->toBeTruthy();
        });

        it('должен быть отрицательным, если вендор указан явным образом', function (): void {
            $moduleId = new ModuleId('bitrix.wizard');
            expect($moduleId->isSystem())->toBeFalsy();
        });
    });

    describe('::isBitrix', function (): void {
        it('должен быть положителен, если вендор не указан явным образом', function (): void {
            $moduleId = new ModuleId('catalog');
            expect($moduleId->isBitrix())->toBeTruthy();
        });

        it('должен быть положителен, если вендор указан явным образом как bitrix', function (): void {
            $moduleId = new ModuleId('bitrix.wizard');
            expect($moduleId->isBitrix())->toBeTruthy();
        });

        it('должен быть отрицателен, если вендор указан явным образом не как bitrix', function (): void {
            $moduleId = new ModuleId('my.wizard');
            expect($moduleId->isBitrix())->toBeFalsy();
        });
    });
});
