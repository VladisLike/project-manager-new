<?php
declare(strict_types=1);

namespace App\Container\Model\User\Service\Tokenizer;

use App\Model\User\Service\Tokenizer\ResetTokenizer;

class ResetTokenizerFactory
{

    /**
     * @param string $interval
     * @return ResetTokenizer
     *
     * @throws \Exception
     */
    public static function create(string $interval): ResetTokenizer
    {
        return new ResetTokenizer(new \DateInterval($interval));
    }

}