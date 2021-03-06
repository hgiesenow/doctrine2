<?php

declare(strict_types=1);

namespace Doctrine\ORM\Query\Expr;

use function count;
use function get_class;
use function implode;
use function in_array;
use function is_string;
use function sprintf;

/**
 * Abstract base Expr class for building DQL parts.
 */
abstract class Base
{
    /** @var string */
    protected $preSeparator = '(';

    /** @var string */
    protected $separator = ', ';

    /** @var string */
    protected $postSeparator = ')';

    /** @var string[] */
    protected $allowedClasses = [];

    /** @var mixed[] */
    protected $parts = [];

    /**
     * @param mixed[] $args
     */
    public function __construct($args = [])
    {
        $this->addMultiple($args);
    }

    /**
     * @param mixed[] $args
     *
     * @return Base
     */
    public function addMultiple($args = [])
    {
        foreach ((array) $args as $arg) {
            $this->add($arg);
        }

        return $this;
    }

    /**
     * @param mixed $arg
     *
     * @return Base
     *
     * @throws \InvalidArgumentException
     */
    public function add($arg)
    {
        if ($arg !== null && (! $arg instanceof self || $arg->count() > 0)) {
            // If we decide to keep Expr\Base instances, we can use this check
            if (! is_string($arg)) {
                $class = get_class($arg);

                if (! in_array($class, $this->allowedClasses, true)) {
                    throw new \InvalidArgumentException(
                        sprintf("Expression of type '%s' not allowed in this context.", $class)
                    );
                }
            }

            $this->parts[] = $arg;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->parts);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->count() === 1) {
            return (string) $this->parts[0];
        }

        return $this->preSeparator . implode($this->separator, $this->parts) . $this->postSeparator;
    }
}
