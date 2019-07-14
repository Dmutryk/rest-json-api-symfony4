<?php

namespace App\Helpers;

use App\Entity\Token;
use App\Managers\TokenManager;

class TokenDisablerHelper
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * TokenDisablerHelper constructor.
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param Token[] $tokens
     */
    public function disableTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            if ($token instanceof Token) {
                $this->tokenManager->delete($token);
            }
        }
    }
}