<?php
namespace App\Services;

use App\Models\User;

class ProfileCompletenessService
{
    public function isComplete(User $user): bool
    {
        if (!$user->phone_verified_at) return false;
        if (!$user->age || $user->age < 18) return false;
        if (!$user->gender) return false;
        if (empty($user->looking_for)) return false;
        if (!$user->location_city || !$user->location_region) return false;
        if (!$user->bio || str_word_count($user->bio) < 20) return false;
        if ($user->practices()->count() === 0) return false;
        return true;
    }

    public function missingFields(User $user): array
    {
        $missing = [];
        if (!$user->phone_verified_at) $missing[] = 'phone_verification';
        if (!$user->age || $user->age < 18) $missing[] = 'age';
        if (!$user->gender) $missing[] = 'gender';
        if (empty($user->looking_for)) $missing[] = 'looking_for';
        if (!$user->location_city || !$user->location_region) $missing[] = 'location';
        if (!$user->bio || str_word_count($user->bio) < 20) $missing[] = 'bio';
        if ($user->practices()->count() === 0) $missing[] = 'practices';
        return $missing;
    }
}
