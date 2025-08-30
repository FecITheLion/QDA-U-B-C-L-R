<?php
//\Countable, \Iterator,
interface Registry extends  \ArrayAccess, \Stringable
{
    /**
     * Registers a value with the registry, automatically generating a unique object key.
     *
     * @param mixed $value The data to store.
     * @return object The newly generated unique object key for this registration.
     */
    public function new(mixed $value): object;

    /**
     * Deregisters an entry by its value.
     *
     * This method will search for the provided value. If exactly one instance of the
     * value is found, it is removed. If multiple instances are found, the value is
     * ambiguous, and the operation will fail.
     *
     * @param mixed $value The value to find and remove. Must be a unique value
     * in the registry.
     * @return void
     * @throws KeyRequiredException If the value exists multiple times in the registry,
     * making it impossible to uniquely identify which one
     * to remove.
     */
    public function not(mixed $value): bool;

    /**
     * Filters the registry's contents using a callback, allowing for LINQ-style queries.
     */
    public function sub(callable $callback): array;
}
