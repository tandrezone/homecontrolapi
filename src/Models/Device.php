<?php

declare(strict_types=1);

namespace HomeControl\Models;

abstract class Device
{
    protected int $id;
    protected string $name;
    protected string $type;
    protected bool $state;
    protected int $roomId;

    public function __construct(int $id, string $name, string $type, bool $state, int $roomId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->state = $state;
        $this->roomId = $roomId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getState(): bool
    {
        return $this->state;
    }

    public function setState(bool $state): void
    {
        $this->state = $state;
    }

    public function toggle(): void
    {
        $this->state = !$this->state;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    abstract public function toArray(): array;
}
