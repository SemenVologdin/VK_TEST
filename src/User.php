<?php

namespace App;

/**
 * Класс для работы с пользователем
 */
class User
{
    /**
     * @var string|mixed
     */
    protected string $name = "";
    /**
     * @var string
     */
    protected string $startLink = "";
    /**
     * @var string
     */
    protected string $endLink = "";
    /**
     * @var string
     */
    protected string $currentLink = "";
    /**
     * @var int
     */
    protected int $attempts = 0;
    /**
     * @var int
     */
    protected int $minAttempts = 0;
    /**
     * @var bool
     */
    protected bool $won = false;
    /**
     * @var bool
     */
    protected bool $showFinishLink = false;

    /**
     * @var Page
     */
    protected Page $page;

    /**
     * @param string $strName
     */
    public function __construct(string $strName = "" )
    {
        $this->name = $strName;

        $this->page = Page::getInstance();
        $this->startLink = $this->currentLink = $this->page->getStartLink();
        $this->endLink = $this->page->getEndLink();
        $this->minAttempts = $this->page->getMinSteps();
    }

    /**
     * @return void
     */
    public function showLinks(): void
    {
        $arLinks = Page::getLinks($this->currentLink);
        print_r("0 {$this->startLink}\n");
        foreach ($arLinks as $key => $strLink){
            print_r($key + 1 . " " . $strLink . "\n");
        }
    }

    /**
     * @param $intLinkNumber
     * @return void
     */
    public function chooseLink($intLinkNumber ): void
    {
        $arLinks = Page::getLinks($this->currentLink);
        $strLink = ( (int)$intLinkNumber === 0 )
            ? $this->getStartLink()
            : $arLinks[$intLinkNumber - 1];

        $this->setCurrentLink($strLink);
        $this->attempts++;
        if( $this->checkIsWon() ){
            $strMessage = "Поздравляем, {$this->getName()}!\n"
                ."Число шагов для поиска - {$this->getAttempts()} шагов!\n"
                ."Минимальное число шагов для поиска - {$this->getMinAttempts()}\n\n";
            print_r($strMessage);
        }
    }

    /**
     * @param $intLinkNumber
     * @return bool
     */
    public function isValidNumber($intLinkNumber ): bool
    {
        if( empty( $intLinkNumber ) || !is_numeric($intLinkNumber) ){
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    public function showFinishLink(): void
    {
        if( $this->showFinishLink ){
            return;
        }

        $strMessage = "Добро пожаловать в игру {$this->getName()}!\n\n"
            ."Стартовая страница - {$this->getStartLink()}\n"
            ."Финишная страница - {$this->getEndLink()}\n\n";

        print_r($strMessage);

        $this->showFinishLink = true;
    }

    /**
     * @return bool
     */
    public function isWon(): bool
    {
        return $this->won;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStartLink(): string
    {
        return $this->startLink;
    }

    /**
     * @param string $startLink
     * @return User
     */
    public function setStartLink(string $startLink): User
    {
        $this->startLink = $startLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndLink(): string
    {
        return $this->endLink;
    }

    /**
     * @param string $endLink
     * @return User
     */
    public function setEndLink(string $endLink): User
    {
        $this->endLink = $endLink;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @param int $attempts
     * @return User
     */
    public function setAttempts(int $attempts): User
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentLink(): string
    {
        return $this->currentLink;
    }

    /**
     * @param string $currentLink
     * @return User
     */
    public function setCurrentLink(string $currentLink): User
    {
        $this->currentLink = $currentLink;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinAttempts(): int
    {
        return $this->minAttempts;
    }

    /**
     * @param int $minAttempts
     * @return User
     */
    public function setMinAttempts(int $minAttempts): User
    {
        $this->minAttempts = $minAttempts;
        return $this;
    }

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     * @return User
     */
    public function setPage(Page $page): User
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @param string $strLink
     * @return bool
     */
    private function compareLink(string $strLink ): bool
    {
        $this->attempts++;

        if( trim(mb_strtolower($this->endLink)) === trim(mb_strtolower($strLink)) ){
            $this->won = true;
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function checkIsWon(): bool
    {
        if( $this->currentLink === $this->endLink ){
            $this->won = true;
            return true;
        }

        return false;
    }
}