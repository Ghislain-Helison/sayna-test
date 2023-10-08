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

    /**
     * Recherche et remplace les placeholders relatifs aux Quote dans le texte
     *
     * @param string $text Le texte a traite
     * @param Quote $quote L'objet Quote actuel
     * @param Site $usefulObject L'objet Site utile
     * @param Destination|null $destination L'objet Destination s'il est present
     *
     * @return string Le texte avec les placeholders des Quote remplacés.
     */

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

    /**
     * Remplace le placeholder '[quote:destination_link]' par le lien de la destination dans le text
     *
     * @param string $text Le texte a traiter
     * @param Site $usefulObject L'objet Site utile
     * @param Destination $destination L'objet Destination
     * @param Quote $quote L'objet Quote actuel
     *
     * @return string Le texte avec le placeholder '[quote:destination_link]' remplace par le lien de la destination
     */
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
