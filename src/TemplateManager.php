<?php

class TemplateManager
{
    /**
     * Type of text to replace
     * @var array
     */
    private $textType = array(
        'quote' => true,
        'user' => true
    );

    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        /**
         * Quote
         * [quote:*]
         */
        if (isset($data['quote']) && $data['quote'] instanceof Quote) {
            $quote = $data['quote'];
            $quoteEntity = QuoteRepository::getInstance()->getById($quote->id);
            $text = $this->replaceText('quote', $text, $quoteEntity);
        }

        /*
         * USER
         * [user:*]
         */
        $applicationContext = ApplicationContext::getInstance();
        $user = (isset($data['user']) && ($data['user'] instanceof User)) ? $data['user'] : $applicationContext->getCurrentUser();
        $text = $this->replaceText('user', $text, $user);

        return $text;
    }

    /**
     * Replaces text by type
     * @param string $type
     * @param string $text
     * @param object $entity
     * @return string
     */
    private function replaceText($type, $text, $entity)
    {
        if ($this->textType[$type] === null) {
            return $text;
        }

        // Look for all user quote
        preg_match_all('/' . $type . ':([a-zA-Z0-9\_]+)/', $text, $matches);

        // Replace text
        $placeholders = $this->getPlaceholdersByType($type);
        foreach ($matches[1] as $match) {
            if ($placeholders[$match] !== null) {
                $replaceQuote = $this->getReplaceTextByType($type, $match, $entity);
                $text = str_replace('[' . $type . ':' . $match . ']', $replaceQuote, $text);
            }
        }

        return $text;
    }

    /**
     * Gets placeholders by type
     * @param string $type
     * @return string on success, otherwise null
     */
    private function getPlaceholdersByType($type)
    {
        $placeholders = null;
        switch ($type) {
            case 'quote':
                $placeholders = Quote::$placeholders;
            break;

            case 'user':
                $placeholders = User::$placeholders;
            break;
        }

        return $placeholders;
    }

    /**
     * Gets replace text by type
     * @param string $type
     * @param string $text
     * @param object $entity
     * @return string
     */
    private function getReplaceTextByType($type, $text, $entity)
    {
        $replaceQuote = '';
        switch ($type) {
            case 'quote':
                $replaceQuote = $entity->getReplaceText($text);
            break;

            case 'user':
                $placeholder = User::$placeholders[$text];
                $replaceQuote = $entity->$placeholder;
            break;
        }

        return $replaceQuote;
    }
}
