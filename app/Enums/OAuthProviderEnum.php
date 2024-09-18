<?php

namespace App\Enums;

enum OAuthProviderEnum: string
{
    case GOOGLE = 'google';
    case FACEBOOK = 'facebook';
    case GITHUB = 'github';
    case LINKEDIN_OPENID = 'linkedin-openid';
    case X = 'x';
    case APPLE = 'apple';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
