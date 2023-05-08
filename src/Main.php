<?php

namespace App;

use Generator;

/**
 * Главный класс самого приложения
 */
class Main
{
    /**
     * @var array
     */
    protected array $users = [];

    /**
     * @return void
     */
    public function init(): void
    {
        $this->users = $this->generateUsers($this->getCountUsers());
    }

    /**
     * @return void
     */
    public function start(): void
    {
        print_r("Счастливой игры, и пусть удача всегда будет с вами!\n");

        while( !$this->isGameOver() ){
            $obUsers = $this->getActiveUsers();
            while( $obUsers->valid() ){
                /** @var User $obUser */
                $obUser = $obUsers->current();
                $obUser->showFinishLink();
                $obUser->showLinks();

                $intNumber = "";
                while( !$obUser->isValidNumber($intNumber) ){
                    $intNumber = readline($obUser->getname() . ", Ваш выбор: ");
                    if( !$obUser->isValidNumber($intNumber) ){
                        print_r("Некорректное число!\nПопробуйте еще раз!\n");
                    }
                }
                $obUser->chooseLink($intNumber);
                $obUsers->next();
            }
        }

        print_r("Пользователь | Число шагов | Минимальное число шагов\n");
        /** @var User $obUser */
        foreach ($this->users as $obUser){
            $strMessage = "{$obUser->getName()} | {$obUser->getAttempts()} | {$obUser->getMinAttempts()}\n";
            print_r($strMessage);
        }

        print_r("\nИгра закончена, поздравляю!");
    }

    /**
     * @return int
     */
    public function getCountUsers(): int
    {
        $intCountUsers = readline("Количество пользователей, учавствующих в игре: ");
        while( !is_numeric($intCountUsers) || $intCountUsers < 0 ){
            print_r("Значение неверное, пожалуйста, укажите целое положительное число!\n");
            $intCountUsers = readline("Количество пользователей, учавствующих в игре: ");
        }

        return (int)$intCountUsers;
    }

    /**
     * @param int $intCountUsers
     * @return array
     */
    public function generateUsers(int $intCountUsers): array
    {
        // region Получение имен пользователей
        $arNames = [];
        for ($i = 0; $i < $intCountUsers; $i++) {
            $strUserName = readline("Введите имя пользователя: ");
            if( mb_strlen($strUserName) === 0 ){
                $i--;
                print_r("Имя пользователя не может быть пустым!");
                continue;
            }
            $strName = trim($strUserName);
            $arNames[] = mb_strtoupper(mb_substr($strName, 0, 1)) . mb_substr($strName, 1);
        }
        // endregion

        print_r("Пожалуйста подождите, идет формирование ссылок!\n");

        // region Формирование массива пользователей
        $arResult = [];

        foreach ($arNames as $strName){
            $arResult[] = new User($strName);
        }
        // endregion

        return $arResult;
    }

    /**
     * @return bool
     */
    private function isGameOver(): bool
    {
        foreach ( $this->users as $obUser){
            if( !$obUser->isWon() ){
                return false;
            }
        }

        return true;
    }

    /**
     * @return Generator
     */
    private function getActiveUsers(): Generator
    {
        foreach ($this->users as $obUser){
            if( !$obUser->isWon() ){
                yield $obUser;
            }
        }
    }
}