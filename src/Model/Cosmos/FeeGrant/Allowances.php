<?php

namespace App\Model\Cosmos\FeeGrant;

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
class Allowances
{
    public string $granter;

    public string $grantee;

    public Allowance $allowance;
}