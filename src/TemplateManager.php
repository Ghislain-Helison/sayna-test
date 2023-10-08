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

    private function replaceQuotePlaceholders($text, $quote, $usefulObject, $destination)
    {
        $containsSummaryHtml = strpos($text, '[quote:summary_html]');
        $containsSummary = strpos($text, '[quote:summary]');

        if ($containsSummaryHtml !== false || $containsSummary !== false) {
            if ($containsSummaryHtml !== false) {
                $text = str_replace('[quote:summary_html]', Quote::renderHtml($quote), $text);
            }
            if ($containsSummary !== false) {
                $text = str_replace('[quote:summary]', Quote::renderText($quote), $text);
            }
        }

        if (isset($destination)) {
            $text = str_replace('[quote:destination_name]', $destination->countryName, $text);
        }

        return $text;
    }

    private function replaceDestinationLink($text, $usefulObject, $destination, $quote)
    {
        $text = str_replace(
            '[quote:destination_link]',
            $usefulObject->url . '/' . $destination->countryName . '/quote/' . $quote->id,
            $text
        );

        return $text;
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);
            $destination = null;

            if (strpos($text, '[quote:destination_link]') !== false) {
                $destination = $destinationOfQuote;
            }

            $text = $this->replaceQuotePlaceholders($text, $_quoteFromRepository, $usefulObject, $destination);

            if (isset($destination)) {
                $text = $this->replaceDestinationLink($text, $usefulObject, $destination, $_quoteFromRepository);
            }
        }

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }

}
