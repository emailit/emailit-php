<?php

namespace Emailit\Service;

use Emailit\EmailitObject;
use Emailit\EmailVerification;

class EmailVerificationService extends AbstractService
{
    /**
     * Verify a single email address.
     *
     * @param array{email: string, mode?: string} $params
     */
    public function verify(array $params): EmailVerification|EmailitObject
    {
        return $this->request('POST', '/v2/email-verifications', $params);
    }
}
