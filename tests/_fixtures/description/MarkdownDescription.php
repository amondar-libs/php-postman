<?php

declare(strict_types = 1);

namespace Tests\_fixtures\description;

use Stringable;

/**
 * Class MarkdownDescription
 *
 * @author Amondar-SO
 */
class MarkdownDescription implements Stringable
{
    public function __toString(): string
    {
        return <<<'MARKDOWN'
                ### Project Overview

                This is a **short** markdown example for testing or documentation purposes.
                
                *   **Task 1**: Initialize repository
                *   **Task 2**: Configure `composer.json`
                *   **Task 3**: Implement `ExportCommand`
                
                > "Efficiency is doing things right; effectiveness is doing the right things."
                
                #### Code Preview
                ```php
                public function handle(): int
                {
                    return Command::SUCCESS;
                }
                ```
               MARKDOWN;
    }
}
