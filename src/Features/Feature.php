<?php

declare(strict_types=1);

namespace HomeControl\Features;

interface Feature
{
    /**
     * Get the unique identifier for this feature.
     */
    public function getId(): string;

    /**
     * Get the human-readable name of the feature.
     */
    public function getName(): string;

    /**
     * Get the description of what this feature does.
     */
    public function getDescription(): string;

    /**
     * Get the current value of the feature.
     */
    public function getValue(): mixed;

    /**
     * Set the value of the feature.
     */
    public function setValue(mixed $value): void;

    /**
     * Get the schema for the feature's value (for validation/documentation).
     */
    public function getSchema(): array;

    /**
     * Check if the feature is currently enabled/active.
     */
    public function isEnabled(): bool;

    /**
     * Enable or disable the feature.
     */
    public function setEnabled(bool $enabled): void;
}