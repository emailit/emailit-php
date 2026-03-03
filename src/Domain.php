<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string|null $uuid
 * @property string $name
 * @property string|null $verification_token
 * @property string|null $verification_method
 * @property string|null $verified_at
 * @property string|null $dkim_identifier_string
 * @property string|null $dns_checked_at
 * @property string|null $spf_status
 * @property string|null $spf_error
 * @property string|null $dkim_status
 * @property string|null $dkim_error
 * @property string|null $mx_status
 * @property string|null $mx_error
 * @property string|null $return_path_status
 * @property string|null $return_path_error
 * @property string|null $dmarc_status
 * @property string|null $dmarc_error
 * @property string|null $tracking_status
 * @property string|null $tracking_error
 * @property string|null $inbound_status
 * @property string|null $inbound_error
 * @property bool|null $track_loads
 * @property bool|null $track_clicks
 * @property array|null $dns_records
 * @property bool|null $deleted
 * @property string $created_at
 * @property string|null $updated_at
 */
class Domain extends ApiResource
{
    const OBJECT_NAME = 'domain';
}
