<?php

namespace App\Test\Controller;

use App\Entity\Enchere;
use App\Repository\EnchereRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EnchereControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EnchereRepository $repository;
    private string $path = '/enchere/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Enchere::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Enchere index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'enchere[prix_initiale]' => 'Testing',
            'enchere[prix_finale]' => 'Testing',
            'enchere[date_creation]' => 'Testing',
            'enchere[date_fermeture]' => 'Testing',
        ]);

        self::assertResponseRedirects('/enchere/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Enchere();
        $fixture->setPrix_initiale('My Title');
        $fixture->setPrix_finale('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setDate_fermeture('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Enchere');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Enchere();
        $fixture->setPrix_initiale('My Title');
        $fixture->setPrix_finale('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setDate_fermeture('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'enchere[prix_initiale]' => 'Something New',
            'enchere[prix_finale]' => 'Something New',
            'enchere[date_creation]' => 'Something New',
            'enchere[date_fermeture]' => 'Something New',
        ]);

        self::assertResponseRedirects('/enchere/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPrix_initiale());
        self::assertSame('Something New', $fixture[0]->getPrix_finale());
        self::assertSame('Something New', $fixture[0]->getDate_creation());
        self::assertSame('Something New', $fixture[0]->getDate_fermeture());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Enchere();
        $fixture->setPrix_initiale('My Title');
        $fixture->setPrix_finale('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setDate_fermeture('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/enchere/');
    }
}
