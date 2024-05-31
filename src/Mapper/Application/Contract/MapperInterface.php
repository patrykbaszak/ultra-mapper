<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Contract;

use PBaszak\UltraMapper\Mapper\Application\Model\Envelope;

interface MapperInterface
{
    public const BLUEPRINT_PROCESS_USE = 'origin';
    public const FROM_PROCESS_USE = 'source';
    public const TO_PROCESS_USE = 'target';

    /**
     * The method maps the data from a array or object to another array or object.
     *
     * @param mixed         $data           the data to map
     * @param mixed         $output         the output of the mapping (not the envelope, the actual mapped data).
     *                                      Yes it can just update the $output variable.
     * @param string        $blueprintClass The class of the blueprint. Defines properties to map.
     * @param TypeInterface $from           you have to define what type are you passing in the $data
     * @param TypeInterface $to             you have to define what type you want to get
     * @param bool          $isCollection   if the data is a collection of objects or arrays then set this to true
     *
     * @return Envelope the mapped data wrapped in the Evelope
     */
    public function map(
        mixed $data,
        mixed &$output,
        string $blueprintClass,
        TypeInterface $from,
        TypeInterface $to,
        bool $isCollection = false
    ): Envelope;
}
