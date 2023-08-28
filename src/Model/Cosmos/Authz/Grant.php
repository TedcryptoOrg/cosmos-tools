<?php

namespace App\Model\Cosmos\Authz;

/**
 * @see https://docs.cosmos.network/swagger/#/Query/GranteeGrants
 *
 * "granter": "string",
 * "grantee": "string",
 * "authorization": {
 * "type_url": "string",
 * "value": "string"
 * },
 * "expiration": "2023-08-28T10:36:24.154Z"
 */
class Grant
{
    public string $granter;

    public string $grantee;

    public Authorization $authorization;

    public string $expiration;
}