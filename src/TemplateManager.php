<?php

class TemplateManager
{
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
            $quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);

            // Look for all quotes
            preg_match_all('/quote:([a-zA-Z0-9\_]+)/', $text, $quoteMatches);

            // Replace quote
            foreach ($quoteMatches[1] as $match) {
                if (Quote::$placeholders[$match] !== null) {
                    $replaceQuote = $quoteFromRepository->getReplaceText($match);
                    $text = $this->replaceQuote('[quote:' . $match . ']', $replaceQuote, $text);
                }
            }
        }

        /*
         * USER
         * [user:*]
         */
        $applicationContext = ApplicationContext::getInstance();
        $user = (isset($data['user']) && ($data['user'] instanceof User)) ? $data['user'] : $applicationContext->getCurrentUser();

        // Look for all user quote
        preg_match_all('/user:([a-zA-Z0-9\_]+)/', $text, $usermatches);

        // Replace user
        foreach ($usermatches[1] as $match) {
            if (User::$placeholders[$match] !== null) {
                $placeholder = User::$placeholders[$match];
                $replaceQuote = $user->$placeholder;
                $text = $this->replaceQuote('[user:' . $match . ']', $replaceQuote, $text);
            }
        }

        return $text;
    }

    /**
     * Replace quote
     * @param string $quote   the string we want to replace
     * @param string $replace the replace string
     * @param string $text
     * @return string
     */
    private function replaceQuote($quote, $replace, $text)
    {
        return str_replace($quote, $replace, $text);
    }
}
