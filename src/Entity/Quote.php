<?php

class Quote
{
    public $id;
    public $siteId;
    public $destinationId;
    public $dateQuoted;

    /**
     * Mapping placeholders to a specific method
     */
    public static $placeholders = array(
        'destination_name' => 'getDestinationName',
        'destination_link' => 'getDestinationLink',
        'summary' => 'getSummary',
        'summary_html' => 'getSummaryHtml'
    );

    public function __construct($id, $siteId, $destinationId, $dateQuoted)
    {
        $this->id = $id;
        $this->siteId = $siteId;
        $this->destinationId = $destinationId;
        $this->dateQuoted = $dateQuoted;
    }

    /**
     * Gets replace text
     * @param string $placeholder
     * @return string
     */
    public function getReplaceText($placeholder)
    {
        $method = self::$placeholders[$placeholder];

        return call_user_func(array(get_class($this), $method));
    }

    /**
     *
     * PRIVATE METHODS
     *
     */

    /**
     * Gets destination name
     * @return string
     */
    private function getDestinationName()
    {
        return DestinationRepository::getInstance()->getById($this->destinationId)->countryName;
    }

    /**
     * Gets destination link
     * @return string
     */
    private function getDestinationLink()
    {
        return SiteRepository::getInstance()->getById($this->siteId)->url . '/' . DestinationRepository::getInstance()->getById($this->destinationId)->countryName . '/quote/' . $this->id;
    }

    /**
     * Gets summary
     * @return string
     */
    private function getSummary()
    {
        return (string) $this->id;
    }

    /**
     * Gets summary html
     * @return string
     */
    private function getSummaryHtml()
    {
        return '<p>' . $this->id . '</p>';
    }
}