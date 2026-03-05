<?php

declare(strict_types=1);

namespace HomeControl\Features;

class FeatureRegistry
{
    private static array $features = [];

    /**
     * Register a feature class.
     */
    public static function register(string $featureClass): void
    {
        if (!class_exists($featureClass)) {
            throw new \InvalidArgumentException("Feature class {$featureClass} does not exist.");
        }

        if (!is_subclass_of($featureClass, Feature::class)) {
            throw new \InvalidArgumentException("Feature class {$featureClass} must implement Feature interface.");
        }

        $instance = new $featureClass();
        self::$features[$instance->getId()] = $featureClass;
    }

    /**
     * Get all registered features.
     */
    public static function getAllFeatures(): array
    {
        return array_keys(self::$features);
    }

    /**
     * Get a feature instance by ID.
     */
    public static function getFeature(string $id, mixed $initialValue = null): ?Feature
    {
        if (!isset(self::$features[$id])) {
            return null;
        }

        $featureClass = self::$features[$id];
        return new $featureClass($initialValue);
    }

    /**
     * Get feature information for API responses.
     */
    public static function getFeatureInfo(string $id): ?array
    {
        $feature = self::getFeature($id);
        return $feature ? $feature->toArray() : null;
    }

    /**
     * Get all feature information.
     */
    public static function getAllFeatureInfo(): array
    {
        $info = [];
        foreach (self::$features as $id => $class) {
            $feature = new $class();
            $info[] = $feature->toArray();
        }
        return $info;
    }
}