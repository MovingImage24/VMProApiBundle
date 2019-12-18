<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Parser;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Get empty configuration set.
     */
    protected function getEmptyConfig(): array
    {
        $yaml = <<<'EOF'
vm_pro_api:
    credentials:
        username:  ~
        password:  ~
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * Get empty configuration set.
     */
    protected function getFullConfig(): array
    {
        $yaml = <<<'EOF'
vm_pro_api:
    base_url:      http://google.com/
    default_vm_id: 5
    credentials:
        username:  test@test.com
        password:  test_password
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
