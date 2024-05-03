<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Api\Data;

interface ConfigInterface
{
    /**
     * Enabled config path
     */
    public const XML_PATH_ENABLED = 'space/basic/enabled';

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;
}
