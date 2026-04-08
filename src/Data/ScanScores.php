<?php

declare(strict_types=1);

namespace ScanLyser\Data;

readonly class ScanScores
{
    public function __construct(
        public float $overall,
        public float $wcag,
        public float $seo,
        public float $performance,
        public float $ux,
        public float $sitewide,
        public float $other,
    ) {}

    /**
     * Create from API response data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            overall: (float) $data['overall'],
            wcag: (float) $data['wcag'],
            seo: (float) $data['seo'],
            performance: (float) $data['performance'],
            ux: (float) $data['ux'],
            sitewide: (float) $data['sitewide'],
            other: (float) $data['other'],
        );
    }
}
