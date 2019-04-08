<?php

namespace App\Feed\Creators;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DateCreator extends Creator
{
    public function canCreate($rubric)
    {
        return $rubric === 'date';
    }

    public function getTitle($item)
    {
        return $this->translator->trans('Event: %title%', ['%title%' => $item->getTitle()], 'rss');
    }

    public function getDescription($item)
    {
        return $this->textConverter->textFullHTMLFormatting($item->getDescription());
    }

    public function getLink($item)
    {
        return $this->router->generate('app_date_detail', [
            'roomId' => $item->getContextId(),
            'itemId' => $item->getItemId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}