<?php

namespace App\Rules;

use App\Models\Subscription;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EmailCheckInPayment implements ValidationRule
{
    public function __construct(
        protected ?int $planId = null,
        protected ?int $serviceId = null
    ) {}
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Subscription::where('email', $value)
            ->where('plan_id', $this->planId)
            ->where('service_id', $this->serviceId)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            $fail("This email is already subscribed.");
        }
    }
}
