<?php

namespace App\Services\NewsProviders;
use App\Models\Source;

interface NewsProviderInterface {
    /** fetch raw items, support pagination via $params */
    public function fetch(array $params = []): array;

    /** normalize raw provider item -> unified payload */
    public function mapToArticlePayload(array $raw, Source $source): array;

    public function getProviderName(): string;
}
