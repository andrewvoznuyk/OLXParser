<?php

namespace App\Services;

use App\Contracts\LinkValidatorInterface;

class LinkValidatorService implements LinkValidatorInterface
{
    /**
     * @param string $link
     * @return bool
     */
    public function validateLink(string $link): bool
    {
        if (str_starts_with($link, 'https://www.olx')) {
            return true;
        }

        return false;
    }
}
