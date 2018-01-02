<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Helper;

use CosmonovaRnD\Auth\Exception\JsonParseException;

/**
 * Class JsonParser
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Http
 * Cosmonova | Research & Development
 */
class JsonParser
{
    /**
     * @param string $json
     *
     * @return array
     * @throws \CosmonovaRnD\Auth\Exception\JsonParseException
     */
    public static function parse(string $json): array
    {
        $jsonData = \json_decode($json, true);

        $error = \json_last_error();

        if (JSON_ERROR_NONE !== $error) {
            throw new JsonParseException(\json_last_error_msg());
        }

        return $jsonData;
    }
}
