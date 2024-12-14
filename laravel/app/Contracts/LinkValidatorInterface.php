<?php

namespace App\Contracts;
interface LinkValidatorInterface
{

    public function validateLink(string $link);

}
