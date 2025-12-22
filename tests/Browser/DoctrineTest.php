<?php

namespace DebugBar\Bridge\Doctrine\Tests\Browser;

class DoctrineTest extends AbstractBrowserTestcase
{
    public function testMonologCollector(): void
    {
        $client = static::createPantherClient();

        $client->request('GET', '/demo/');

        // Wait for Debugbar to load
        $crawler = $client->waitFor('.phpdebugbar-body');
        usleep(1000);

        if (!$this->isTabActive($crawler, 'database')) {
            $client->click($this->getTabLink($crawler, 'database'));
        }

        $crawler = $client->waitForVisibility('.phpdebugbar-panel[data-collector=database]');

        $statements = $crawler->filter('.phpdebugbar-panel[data-collector=database] .phpdebugbar-widgets-sql')
            ->each(function($node){
                return $node->getText();
            });

        $this->assertEquals('INSERT INTO products (name, updated) VALUES (?, ?)', $statements[2]);
        $this->assertCount(6, $statements);
    }
}
