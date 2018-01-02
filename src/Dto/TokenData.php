<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Dto;

/**
 * Class TokenData
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Http
 * Cosmonova | Research & Development
 */
class TokenData
{
    /**
     * @var string
     */
    public $accessToken;
    /**
     * @var string
     */
    public $expiresIn;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $refreshToken;

    /**
     * TokenData constructor.
     *
     * @param string      $accessToken
     * @param int         $expiresIn
     * @param string      $type
     * @param string|null $refreshToken
     */
    public function __construct(string $accessToken, int $expiresIn, string $type, string $refreshToken = null)
    {
        $this->accessToken  = $accessToken;
        $this->expiresIn    = $expiresIn;
        $this->type         = $type;
        $this->refreshToken = $refreshToken;
    }
}
