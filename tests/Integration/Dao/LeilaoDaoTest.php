<?php

namespace Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = ConnectionCreator::getConnection();
    }

    public function testInsercaoEBuscaDevemFuncionar()
    {
        $leilao = new Leilao('Variante 0Km');
        $leilaoDao = new LeilaoDao($this->pdo);

        $leilaoDao->salva($leilao);
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame(
            'Variante 0Km',
            $leiloes[0]->recuperarDescricao()
        );
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DELETE FROM leiloes');
    }
}

/*
 * 1.6 Inserindo e buscando
 * Agora o PHPUnit detecta o erro na query SQL
 *
 * 2.2 Removendo após o teste
 * Usamos a função tearDown() para deletar todos os dados do banco e poder normalizar os testes.
 * Contudo, apagar todos os dados do banco não é recomendado.
 */