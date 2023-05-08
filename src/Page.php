<?php

namespace App;

/**
 * Класс для удобной работы с отображением ссылок со страницы
 */
class Page
{
    /**
     * @var int
     */
    protected int $minSteps;
    /**
     * @var string
     */
    protected string $startLink;
    /**
     * @var string
     */
    protected string $endLink;

    protected array $stack = [];

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        [$strStartLink, $strEndLink, $intSteps, $arStack] = static::generateLinks();

        return (new self())
            ->setStartLink($strStartLink)
            ->setEndLink($strEndLink)
            ->setMinSteps($intSteps)
            ->setStack($arStack)
            ;
    }

    /**
     * @param string $strUrl
     * @param int $intLimit
     * @return array
     */
    public static function getLinks(string $strUrl = "", int $intLimit = 10 ): array
    {
        if( empty( $strUrl ) ){
            return [];
        }

        $intEnvLinkCount = (int)getenv("APP_LINKS_COUNT");
        if( !empty( $intEnvLinkCount ) ){
            $intLimit = $intEnvLinkCount;
        }

        $obCurl = curl_init($strUrl);
        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($obCurl, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($obCurl);
        curl_close($obCurl);

        preg_match_all('~<a\s+[^>]*?href\s?=[\s\"\']+(.*?)[\"\']+.*?>([^<]+|.*?)?</a>~', $content, $arMatches);

        if( empty( $arMatches ) ){
            return [];
        }

        $arLinks = array();

        foreach ($arMatches as $mixLink){
            if( empty( $mixLink ) ){
                continue;
            }

            if( is_array($mixLink) ){

                $arLinks = array_merge($arLinks, $mixLink);
            }else{
                $arLinks[] = $mixLink;
            }
        }

        $arLinks = array_unique($arLinks);

        foreach( $arLinks as $key => &$strLink ){

            switch(true){
                case empty($strLink):
                case !str_contains($strLink, "href=\"/wiki/"):
                case !str_contains($strLink, "title=\""):
                case str_contains($strLink, ":"):
                case str_contains($strLink, "ISBN"):
                    unset($arLinks[$key]);
                    continue 2;
            }

            preg_match( "~href=\"(\S+)\"~", $strLink, $arMatch);
            $strLink = "https://en.wikipedia.org" . $arMatch[1];
        }

        $intCountLinks = count($arLinks);
        if( $intCountLinks / 2 >= $intLimit){
            $intFrom = (int)(count($arLinks) / 2);
            return array_values(array_splice($arLinks, $intFrom ,$intLimit));
        }elseif ($intCountLinks > $intLimit){
            return array_values(array_splice($arLinks, 0 ,$intLimit));
        }

        return array_values($arLinks);
    }

    /**
     * @return array
     */
    private static function generateLinks(): array
    {
        [$strStartLink, $_] = static::getRandomLinkBySteps();
        $intSteps = rand(1, 3);
        [$strEndLink, $arStack] = static::getRandomLinkBySteps($strStartLink, $intSteps);
        return [$strStartLink, $strEndLink, $intSteps, $arStack];
    }

    /**
     * @param string $strStartLink
     * @param ?int $intSteps
     * @return array
     */
    private static function getRandomLinkBySteps(string $strStartLink = "https://en.wikipedia.org/wiki/Main_Page", ?int $intSteps = null ):array
    {
        $intSteps = (int)getenv('APP_DEEP_LEVEL') ?: 3;
        $arStack = [];

        $strLink = $strStartLink;
        for ($i = 0; $i < $intSteps ; $i++){
            $arLinks = static::getLinks($strLink);
            $strLink = $arLinks[rand(0, count($arLinks)-1)];
            $arStack[] = $strLink;
        }
        return [$strLink, $arStack];
    }

    /**
     * @return int
     */
    public function getMinSteps(): int
    {
        return $this->minSteps;
    }

    /**
     * @param int $minSteps
     * @return Page
     */
    public function setMinSteps(int $minSteps): Page
    {
        $this->minSteps = $minSteps;
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
     * @return Page
     */
    public function setStartLink(string $startLink): Page
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
     * @return Page
     */
    public function setEndLink(string $endLink): Page
    {
        $this->endLink = $endLink;
        return $this;
    }

    /**
     * @return array
     */
    public function getStack(): array
    {
        return $this->stack;
    }

    /**
     * @param array $stack
     * @return Page
     */
    public function setStack(array $stack): Page
    {
        $this->stack = $stack;
        return $this;
    }
}