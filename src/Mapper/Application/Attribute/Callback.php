<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Mapper\Application\Attribute;

use PBaszak\UltraMapper\Mapper\Application\Contract\AttributeInterface;
use PBaszak\UltraMapper\Mapper\Application\Exception\ThrowAttributeValidationExceptionTrait;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Callback implements AttributeInterface
{
    use ThrowAttributeValidationExceptionTrait;

    public const STAGE_0A_INITIALIZATION = 0; // max 1
    public const STAGE_0B_CALLBACK_IF_NOT_EXISTS = 1;
    public const STAGE_0C_INITIALIZATION_IF_NOT_EXISTS = 2; // max 1
    public const STAGE_1_CALLBACK = 3;
    public const STAGE_2_ASSIGNMENT = 4; // max 1
    public const STAGE_3_CALLBACK_ON_FAILURE = 5;
    public const STAGE_4_FINAL_CALLBACK = 6;

    /**
     * The callback attribute allows you to define your own callback function that will be executed during the mapping process.
     *
     * ### Option 1 - has custom initialization
     * {{callback.STAGE_0A_INITIALIZATION}} - requires $var assignment using: {{var.name}} = callback
     * {{callback.STAGE_1_CALLBACK}} - requires using {{var.name}}
     * try {
     *      {{callback.STAGE_2_ASSIGNMENT}} - the semicolon is not allowed. Think this way: {{target.name}}['{{target.propertyName}}'] = {{callback.STAGE_2_ASSIGNMENT}};
     *                                        {{source.getter}} or {{var.name}} are required
     * } catch (\Throwable {{exception.name}}) {
     *     {{callback.STAGE_3_CALLBACK_ON_FAILURE}}
     * }
     * {{callback.STAGE_4_FINAL_CALLBACK}}
     *
     *
     * ### Option 2 - has no default value
     * if ({{source.isExists}}) {
     *    {{var.setter}} # require only if stage_1 is used
     *    {{callback.STAGE_1_CALLBACK}} - requires using $var variable
     *    try {
     *        {{callback.STAGE_2_ASSIGNMENT}}
     *    } catch (\Throwable {{exception.name}}) {
     *        {{callback.STAGE_3_CALLBACK_ON_FAILURE}}
     *    }
     * } else {
     *    {{callback.STAGE_0B_CALLBACK_IF_NOT_EXISTS}}
     * }
     * {{callback.STAGE_4_FINAL_CALLBACK}}
     *
     *
     * ### Option 3 - has default value
     * if ({{source.isExists}}) {
     *    {{var.setter}}
     * } else {
     *    {{var.setter}} // but with default value
     * }
     * {{callback.STAGE_1_CALLBACK}} - requires using $var variable
     * try {
     *   {{callback.STAGE_2_ASSIGNMENT}}
     * } catch (\Throwable {{exception.name}}) {
     *  {{callback.STAGE_3_CALLBACK_ON_FAILURE}}
     * }
     * {{callback.STAGE_4_FINAL_CALLBACK}}
     *
     * ### Option 4 - has initialization if not exists
     * if ({{source.isExists}}) {
     *   {{var.setter}}
     * } else {
     *  {{callback.STAGE_0C_INITIALIZATION_IF_NOT_EXISTS}} - requires $var assignment using: {{var.name}} = callback
     *                                                       the default value is allowed under `{{target.defaultValue}}`
     * }
     * {{callback.STAGE_1_CALLBACK}} - requires using $var variable
     * try {
     *   {{callback.STAGE_2_ASSIGNMENT}}
     * } catch (\Throwable {{exception.name}}) {
     *  {{callback.STAGE_3_CALLBACK_ON_FAILURE}}
     * }
     * {{callback.STAGE_4_FINAL_CALLBACK}}
     */
    public function __construct(
        /**
         * @var callable-string $callback
         */
        public string $callback,
        /**
         * @var int $stage                  There are 5 mapping stages in which you can invoke your own callback:
         *          - Stage 0 Value initialization, which takes place in one of 2 ways:
         *          a) getting the value from the source variable (if it is unavailable, a dedicated callback will be invoked. E.g. assigning a default value)
         *          b) assigning a value as part of the initialization callback, bypassing the source data
         *          - Stage 1 Any callback on the data using the `$var` variable.
         *          - Stage 2 Assigning a value to the target variable in the `try catch` block
         *          - Stage 3 Reaction to failed value assignment
         *          - Stage 4 Arbitrary callback to complete property mapping. Ex. logging
         */
        public int $stage,
        /**
         * @var int $priority               The priority of the callback. The higher the value, the earlier the callback will be executed.
         */
        public int $priority = 0,
        /**
         * @var array<string, mixed> Options are for modificators of the mapping process. If You need them, You can use them.
         */
        public array $options = []
    ) {
    }

    public function validate(\ReflectionProperty|\ReflectionClass $reflection): void
    {
        // todo: update the list of available placeholders
        // if (str_replace([
        //     '$var',
        //     '$source',
        //     '$target',
        //     '$item',
        // ], '', $this->callback) !== $this->callback) {
        //     $this->throwException('You should use {{var}}, {{source}}, {{target}} or {{item}} placeholders in your callback string.', 5953, $reflection);
        // }

        // if ($this->stage < self::STAGE_0A_INITIALIZATION || $this->stage > self::STAGE_4_FINAL_CALLBACK) {
        //     $this->throwException('Stage should be between 0 and 4. Check the Callback class constants for available stages.', 5954, $reflection);
        // }

        // if (in_array(
        //     $this->stage,
        //     [
        //         self::STAGE_0A_INITIALIZATION,
        //         self::STAGE_1_CALLBACK
        //     ]
        // ) && false === str_contains($this->callback, '{{var.name}}')) {
        //     $this->throwException('You have to use {{var.name}} placeholder in your callback string. You should think about {{var.name}} as `$var` variable.', 5955, $reflection);
        // }

        // if (self::STAGE_2_ASSIGNMENT === $this->stage && $this->callback === str_replace(['{{var.name}}', '{{source.getter}}'], '', $this->callback)) {
        //     $this->throwException('You have to use {{var.name}} and {{source.getter}} placeholders in your callback string.', 5956, $reflection);
        // }
    }
}
