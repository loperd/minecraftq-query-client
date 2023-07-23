<?php

declare(strict_types=1);

namespace Loper\MinecraftQueryClient\Common\Query;

final class ChallengeToken
{
    public function __construct(
        public readonly int $token,
        public readonly int $sessionId
    ) {
    }
}
