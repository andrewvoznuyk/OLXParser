<?php

namespace App\Contracts;
interface LinkParserServiceInterface
{
    public function parse(string $link): self;
    public function getLinkData(string $link);
}
